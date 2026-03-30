<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderReceiverNotification;
use App\Services\Barobill\BarobillFaxFtpService;
use App\Services\Barobill\BarobillFaxService;
use App\Services\Barobill\BarobillKakaotalkService;
use App\Services\OrderFaxPdfService;
use App\Services\ShopDisplayNameService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DispatchOrderReceiverNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public int $notificationId)
    {
        $this->afterCommit();
    }

    public function handle(
        OrderFaxPdfService $pdfService,
        BarobillFaxFtpService $ftpService,
        BarobillFaxService $faxService,
        BarobillKakaotalkService $kakaotalkService,
        ShopDisplayNameService $shopDisplayNameService
    ): void {
        $notification = OrderReceiverNotification::query()
            ->with(['order.ordererShop', 'order.receiverShop', 'receiverShop'])
            ->findOrFail($this->notificationId);

        if (in_array($notification->status, ['sent', 'skipped', 'superseded'], true)) {
            return;
        }

        $order = $notification->order;
        $receiverShop = $notification->receiverShop;

        if (!$order || !$receiverShop) {
            throw new \RuntimeException('수주사 알림 작업에 필요한 주문 또는 수주사 정보가 없습니다.');
        }

        if ((int) ($order->receiver_shop_id ?? 0) !== (int) $notification->receiver_shop_id) {
            $notification->update([
                'status' => 'superseded',
                'error_message' => '주문의 현재 수주사가 변경되어 이전 알림 작업을 중단했습니다.',
            ]);

            return;
        }

        $faxEnabled = (bool) config('barobill.fax.enabled');

        $notification->update([
            'status' => 'processing',
            'fax_status' => $faxEnabled ? 'processing' : 'skipped',
            'kakaotalk_status' => 'pending',
            'error_message' => null,
        ]);

        $pdfPath = null;
        $faxStatus = $faxEnabled ? 'processing' : 'skipped';

        try {
            if ($faxEnabled) {
                $pdfPath = $pdfService->generate($order, $receiverShop);
                $faxFileName = $ftpService->uploadPdf($pdfPath, $order->order_no);

                $notification->update([
                    'fax_file_name' => $faxFileName,
                ]);

                if (!$this->ensureCurrentReceiver($order, $notification)) {
                    return;
                }

                $faxSendKey = $faxService->sendFromFtp(
                    fileName: $faxFileName,
                    toNumber: (string) $receiverShop->fax,
                    receiveCorp: (string) $receiverShop->shop_name,
                    receiveName: (string) ($receiverShop->owner_name ?: $receiverShop->shop_name),
                    refKey: 'receiver-assignment-' . $notification->id,
                );

                $faxResult = $faxService->waitUntilCompleted($faxSendKey);
                $faxStatus = 'sent';

                $notification->update([
                    'fax_status' => 'sent',
                    'fax_send_key' => $faxSendKey,
                    'fax_response' => json_encode($faxResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'fax_sent_at' => now(),
                ]);
            } else {
                $notification->update([
                    'fax_status' => 'skipped',
                    'fax_response' => json_encode([
                        'status' => 'skipped',
                        'reason' => 'disabled',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }

            $kakaotalkResult = $kakaotalkService->sendAssignmentNotice(
                order: $order,
                receiverShop: $receiverShop,
                refKey: 'receiver-assignment-' . $notification->id,
            );

            $kakaotalkStatus = (string) ($kakaotalkResult['status'] ?? 'skipped');
            $notification->update([
                'status' => $this->resolveCompletionStatus($faxStatus, $kakaotalkStatus),
                'fax_status' => $faxStatus,
                'kakaotalk_status' => $kakaotalkStatus,
                'kakaotalk_send_key' => $kakaotalkStatus === 'sent' ? (string) ($kakaotalkResult['send_key'] ?? '') : null,
                'kakaotalk_response' => json_encode($kakaotalkResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'kakaotalk_sent_at' => $kakaotalkStatus === 'sent' ? now() : null,
            ]);

            $notification->refresh();

            if ($this->shouldRecordSuccessHistory($notification)) {
                $this->recordSuccessHistory($notification, $shopDisplayNameService);
            } else {
                $notification->update([
                    'history_recorded_at' => now(),
                ]);
            }
        } catch (Throwable $e) {
            $errorSummary = $this->buildErrorSummary($e);

            $notification->update([
                'status' => 'failed',
                'fax_status' => $notification->fax_sent_at ? 'sent' : 'failed',
                'kakaotalk_status' => $notification->kakaotalk_sent_at
                    ? 'sent'
                    : ($notification->fax_sent_at ? 'failed' : $notification->kakaotalk_status),
                'error_message' => $errorSummary,
            ]);

            Log::error('Receiver fax dispatch failed.', [
                'notification_id' => $notification->id,
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'receiver_shop_id' => $receiverShop->id,
                'receiver_shop_name' => $receiverShop->shop_name,
                'receiver_shop_fax' => $receiverShop->fax,
                'fax_file_name' => $notification->fax_file_name,
                'fax_send_key' => $notification->fax_send_key,
                'fax_status' => $notification->fax_status,
                'kakaotalk_status' => $notification->kakaotalk_status,
                'barobill_mode' => config('barobill.mode'),
                'barobill_fax_enabled' => config('barobill.fax.enabled'),
                'barobill_wsdl' => config('barobill.fax.wsdl'),
                'barobill_from_number' => config('barobill.fax.from_number'),
                'error_message' => $errorSummary,
                'exception_class' => $e::class,
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
            ]);

            throw $e;
        } finally {
            if ($pdfPath && is_file($pdfPath)) {
                @unlink($pdfPath);
            }
        }
    }

    public function failed(Throwable $e): void
    {
        $notification = OrderReceiverNotification::query()
            ->with(['order', 'receiverShop'])
            ->find($this->notificationId);

        if (!$notification || !$notification->order || !$notification->receiverShop) {
            return;
        }

        if ($notification->status === 'superseded') {
            return;
        }

        $shopDisplayNameService = app(ShopDisplayNameService::class);
        $receiverDisplayName = $shopDisplayNameService->format($notification->receiverShop);
        $reason = $this->summarizeReason($notification->error_message ?: $e->getMessage());
        $message = '수주사 <strong>' . $receiverDisplayName . '</strong> 팩스 발송 실패';

        if ($notification->fax_status === 'sent' && $notification->kakaotalk_status === 'failed') {
            $message = '수주사 <strong>' . $receiverDisplayName . '</strong> 팩스 발송 완료, 알림톡 발송 실패';
        } elseif ($notification->fax_status === 'skipped' && $notification->kakaotalk_status === 'failed') {
            $message = '수주사 <strong>' . $receiverDisplayName . '</strong> 알림톡 발송 실패';
        }

        if ($reason !== '') {
            $message .= ' (사유: ' . $reason . ')';
        }

        OrderHistory::create([
            'order_id' => $notification->order->id,
            'order_no' => $notification->order->order_no,
            'history_type' => 'updated',
            'message' => $message,
            'processed_at' => now(),
            'actor_user_id' => $notification->actor_user_id,
        ]);
    }

    protected function ensureCurrentReceiver(Order $order, OrderReceiverNotification $notification): bool
    {
        $currentReceiverShopId = Order::query()
            ->whereKey($order->id)
            ->value('receiver_shop_id');

        if ((int) $currentReceiverShopId !== (int) $notification->receiver_shop_id) {
            $notification->update([
                'status' => 'superseded',
                'error_message' => '주문의 현재 수주사가 변경되어 팩스 전송을 중단했습니다.',
            ]);

            return false;
        }

        return true;
    }

    protected function recordSuccessHistory(
        OrderReceiverNotification $notification,
        ShopDisplayNameService $shopDisplayNameService
    ): void {
        if ($notification->history_recorded_at) {
            return;
        }

        DB::transaction(function () use ($notification, $shopDisplayNameService) {
            $freshNotification = OrderReceiverNotification::query()
                ->lockForUpdate()
                ->with(['order', 'receiverShop'])
                ->findOrFail($notification->id);

            if ($freshNotification->history_recorded_at) {
                return;
            }

            if (!$this->shouldRecordSuccessHistory($freshNotification)) {
                $freshNotification->update([
                    'history_recorded_at' => now(),
                ]);

                return;
            }

            $receiverDisplayName = $shopDisplayNameService->format($freshNotification->receiverShop);
            $message = '수주사 <strong>' . $receiverDisplayName . '</strong> 팩스 발송 완료';

            if ($freshNotification->fax_status === 'sent' && $freshNotification->kakaotalk_status === 'sent') {
                $message = '수주사 <strong>' . $receiverDisplayName . '</strong> 팩스와 알림톡 발송 완료';
            } elseif ($freshNotification->kakaotalk_status === 'sent') {
                $message = '수주사 <strong>' . $receiverDisplayName . '</strong> 알림톡 발송 완료';
            }

            OrderHistory::create([
                'order_id' => $freshNotification->order->id,
                'order_no' => $freshNotification->order->order_no,
                'history_type' => 'updated',
                'message' => $message,
                'processed_at' => now(),
                'actor_user_id' => $freshNotification->actor_user_id,
            ]);

            $freshNotification->update([
                'history_recorded_at' => now(),
            ]);
        });
    }

    protected function summarizeReason(?string $reason): string
    {
        $reason = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $reason)));

        if ($reason === '') {
            return '';
        }

        return Str::limit($reason, 120);
    }

    protected function shouldRecordSuccessHistory(OrderReceiverNotification $notification): bool
    {
        return $notification->fax_status === 'sent' || $notification->kakaotalk_status === 'sent';
    }

    protected function resolveCompletionStatus(string $faxStatus, string $kakaotalkStatus): string
    {
        if ($faxStatus === 'failed' || $kakaotalkStatus === 'failed') {
            return 'failed';
        }

        if ($faxStatus === 'sent' || $kakaotalkStatus === 'sent') {
            return 'sent';
        }

        if ($faxStatus === 'skipped' && $kakaotalkStatus === 'skipped') {
            return 'skipped';
        }

        return 'sent';
    }

    protected function buildErrorSummary(Throwable $e): string
    {
        $message = trim($e->getMessage());

        if ($message === '') {
            $message = '예외 메시지 없음';
        }

        return $e::class . ' @ ' . basename($e->getFile()) . ':' . $e->getLine() . ' - ' . $message;
    }
}
