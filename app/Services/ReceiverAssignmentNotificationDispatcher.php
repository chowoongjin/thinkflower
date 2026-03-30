<?php

namespace App\Services;

use App\Jobs\DispatchOrderReceiverNotificationJob;
use App\Models\Order;
use App\Models\OrderReceiverNotification;
use Illuminate\Support\Facades\DB;

class ReceiverAssignmentNotificationDispatcher
{
    public function dispatch(Order $order, int $receiverShopId, ?int $actorUserId): OrderReceiverNotification
    {
        $notification = $this->createNotification($order, $receiverShopId, $actorUserId);

        if (!(bool) config('barobill.fax.enabled') && !(bool) config('barobill.kakaotalk.enabled')) {
            $notification->update([
                'status' => 'skipped',
                'fax_status' => 'skipped',
                'kakaotalk_status' => 'skipped',
                'fax_response' => json_encode([
                    'status' => 'skipped',
                    'reason' => 'disabled',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'kakaotalk_response' => json_encode([
                    'status' => 'skipped',
                    'reason' => 'disabled',
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'error_message' => '모든 수주사 알림 발송이 비활성화되어 작업을 건너뛰었습니다.',
                'history_recorded_at' => now(),
            ]);

            return $notification;
        }

        DispatchOrderReceiverNotificationJob::dispatch($notification->id)->afterCommit();

        return $notification;
    }

    protected function createNotification(Order $order, int $receiverShopId, ?int $actorUserId): OrderReceiverNotification
    {
        return DB::transaction(function () use ($order, $receiverShopId, $actorUserId) {
            OrderReceiverNotification::query()
                ->where('order_id', $order->id)
                ->whereIn('status', ['pending', 'processing', 'failed'])
                ->update([
                    'status' => 'superseded',
                    'error_message' => '새 수주사 지정으로 이전 알림 작업이 대체되었습니다.',
                    'updated_at' => now(),
                ]);

            $notification = OrderReceiverNotification::create([
                'order_id' => $order->id,
                'receiver_shop_id' => $receiverShopId,
                'actor_user_id' => $actorUserId,
                'status' => 'pending',
                'fax_status' => 'pending',
                'kakaotalk_status' => 'pending',
            ]);

            return $notification;
        });
    }
}
