<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\OrderPhoto;
use App\Models\OrderHistory;
use App\Jobs\ProcessCafe24UploadJob;
use App\Models\UploadTask;
use App\Services\Cafe24FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AllOrderListController extends Controller
{
    protected Cafe24FileUploadService $uploadService;
    public function __construct(Cafe24FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? $request->input('date_from')
            : Carbon::today()->subDays(15)->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->input('date_to')
            : Carbon::today()->addDays(15)->format('Y-m-d');

        $productType = trim((string) $request->input('product_type', '전체상품'));
        $statusType = trim((string) $request->input('status_type', '전체상태'));
        $orderNo = trim((string) $request->input('order_no', ''));
        $recipientName = trim((string) $request->input('recipient_name', ''));
        $deliveryAddr = trim((string) $request->input('delivery_addr', ''));
        $rangePreset = trim((string) $request->input('range_preset', ''));

        $query = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->withCount('photos')
            ->whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo);

        if ($statusType === '삭제처리') {
            $query->where('is_hidden', 1);
        } else {
            $query->where('is_hidden', 0);
        }

        if ($productType !== '' && $productType !== '전체상품') {
            if ($productType === '근조화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '근조3단%')
                        ->orWhere('product_name', 'like', '근조화환%');
                });
            } elseif ($productType === '축하화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '축하3단%')
                        ->orWhere('product_name', 'like', '축하화환%');
                });
            } else {
                $query->where('product_name', 'like', '%' . $productType . '%');
            }
        }

        if ($statusType !== '' && $statusType !== '전체상태') {
            if ($statusType === '중개필요') {
                $query->where('brokerage_type', 'waiting');
            } elseif ($statusType === '주문접수') {
                $query->where('current_status', 'accepted');
            } elseif ($statusType === '배송완료') {
                $query->where('current_status', 'delivered');
            } elseif ($statusType === '주문취소') {
                $query->where('current_status', 'cancelled');
            }
        }

        if ($orderNo !== '') {
            $query->where('order_no', 'like', '%' . $orderNo . '%');
        }

        if ($recipientName !== '') {
            $query->where('recipient_name', 'like', '%' . $recipientName . '%');
        }

        if ($deliveryAddr !== '') {
            $query->where('delivery_addr1', 'like', '%' . $deliveryAddr . '%');
        }

        $orders = (clone $query)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalOrderAmount = (clone $query)->sum('original_amount');
        $totalPaymentAmount = (clone $query)
            ->where('current_status', 'delivered')
            ->sum('order_amount');

        $data = compact(
            'orders',
            'dateFrom',
            'dateTo',
            'productType',
            'statusType',
            'orderNo',
            'recipientName',
            'deliveryAddr',
            'rangePreset',
            'totalOrderAmount',
            'totalPaymentAmount'
        );

        if ($request->ajax()) {
            return response()->json([
                'table_html' => view('admin.partials.all-order-list-table', $data)->render(),
                'total_order_amount' => number_format($totalOrderAmount),
                'total_payment_amount' => number_format($totalPaymentAmount),
            ]);
        }

        return view('admin.all-order-list', $data);
    }

    public function popup(Order $order)
    {
        abort_if((int) $order->is_hidden === 1, 404);
        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        return view('admin.all-order-popup', [
            'order' => $order,
        ]);
    }

    public function historyModal(Order $order)
    {
        abort_if((int) $order->is_hidden === 1, 404);
        $histories = $order->histories()
            ->orderBy('processed_at')
            ->orderBy('id')
            ->get();

        return view('admin.partials.all-order-history-modal', [
            'order' => $order,
            'histories' => $histories,
        ]);
    }

    public function photoPopup(Order $order)
    {
        abort_if((int) $order->is_hidden === 1, 404);
        $photos = DB::table('order_photos')
            ->where('order_id', $order->id)
            ->orderBy('sort_order')
            ->get();

        return view('pages.order-photo-popup', [
            'order' => $order,
            'photos' => $photos,
        ]);
    }

    public function assignReceiver(Request $request, Order $order)
    {
        $validated = $request->validate([
            'receiver_shop_id' => ['required', 'integer', 'exists:shops,id'],
            'admin_accept' => ['nullable', 'in:1'],
        ], [
            'receiver_shop_id.required' => '수주사를 선택해 주세요.',
            'receiver_shop_id.exists' => '존재하지 않는 수주사입니다.',
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            $order->refresh();

            $oldStatus = $order->current_status;
            $receiverShopId = (int) $validated['receiver_shop_id'];
            $adminAccept = (string) ($validated['admin_accept'] ?? '') === '1';

            $updateData = [
                'receiver_shop_id' => $receiverShopId,
                'brokerage_type' => 'assigned',
            ];

            if ($adminAccept) {
                $updateData['current_status'] = 'accepted';
                $updateData['accepted_at'] = now();
                $updateData['accepted_by_type'] = 'admin';
            }

            $order->update($updateData);

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'history_type' => $adminAccept ? 'accepted' : 'updated',
                'message' => '<strong>본부 수발주사업부</strong> 에서 주문접수 처리',
                'processed_at' => now(),
                'actor_user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $order->id,
                'from_status' => $oldStatus,
                'to_status' => $adminAccept ? 'accepted' : $oldStatus,
                'changed_by_user_id' => auth()->id(),
                'memo' => $adminAccept ? '관리자 본부접수 처리' : '관리자 수주사 지정',
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => ((string) ($validated['admin_accept'] ?? '') === '1')
                ? '수주사 지정 및 본부접수가 완료되었습니다.'
                : '수주사가 지정되었습니다.',
        ]);
    }
    public function accept(Request $request, Order $order)
    {
        $adminUser = $request->user();

        abort_unless($adminUser, 403);

        if ((int) ($order->receiver_shop_id ?? 0) === 0) {
            return response()->json([
                'message' => '수주사가 지정되지 않은 주문입니다.',
            ], 422);
        }

        if ($order->current_status === 'delivered') {
            return response()->json([
                'message' => '배송완료된 주문건 입니다.',
            ], 422);
        }

        DB::transaction(function () use ($order, $adminUser) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $fromStatus = $lockedOrder->current_status;

            if ((int) ($lockedOrder->receiver_shop_id ?? 0) === 0) {
                abort(422, '수주사가 지정되지 않은 주문입니다.');
            }

            if ($lockedOrder->current_status === 'delivered') {
                abort(422, '배송완료된 주문건 입니다.');
            }

            $lockedOrder->update([
                'brokerage_type' => 'assigned',
                'current_status' => 'accepted',
                'accepted_at' => now(),
                'accepted_by_type' => 'admin',
                'assigned_by_admin_user_id' => $adminUser->id,
            ]);

            DB::table('order_histories')->insert([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'accepted',
                'message' => '<strong>본부 수발주사업부</strong> 에서 주문접수 처리',
                'processed_at' => now(),
                'actor_user_id' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'accepted',
                'changed_by_user_id' => $adminUser->id,
                'memo' => '본부 주문접수 처리',
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '주문접수 처리되었습니다.',
        ]);
    }
    public function resetBrokerage(Request $request, Order $order)
    {
        $adminUser = $request->user();

        abort_unless($adminUser, 403);

        if ($order->current_status === 'delivered') {
            return response()->json([
                'message' => '배송완료된 주문건 입니다.',
            ], 422);
        }

        DB::transaction(function () use ($order, $adminUser) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $fromStatus = $lockedOrder->current_status;

            if ($lockedOrder->current_status === 'delivered') {
                abort(422, '배송완료된 주문건 입니다.');
            }

            $lockedOrder->update([
                'receiver_shop_id' => null,
                'assigned_by_admin_user_id' => null,
                'brokerage_type' => 'waiting',
                'current_status' => 'submitted',
                'accepted_at' => null,
                'accepted_by_type' => null,
                'delivered_at' => null,
                'receiver_name' => null,
                'receiver_relation' => null,
            ]);

            DB::table('order_histories')->insert([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'updated',
                'message' => '<strong>본부 수발주사업부</strong> 에서 수주사 재선정',
                'processed_at' => now(),
                'actor_user_id' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'submitted',
                'changed_by_user_id' => $adminUser->id,
                'memo' => '수주사지정 초기화',
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '중개대기로 변경되었습니다.',
        ]);
    }

    public function completePopup(Request $request, Order $order)
    {
        abort_if((int) $order->is_hidden === 1, 404);
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'hq'], true), 403);

        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        $photos = DB::table('order_photos')
            ->where('order_id', $order->id)
            ->orderBy('sort_order')
            ->get();

        $photoShop = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 1;
        });

        $photoSite = $photos->first(function ($photo) {
            return $photo->photo_type === 'delivery_site';
        });

        $photoExtra = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 3;
        });

        return view('pages.suju-complete-popup', [
            'order' => $order,
            'photoShop' => $photoShop,
            'photoSite' => $photoSite,
            'photoExtra' => $photoExtra,
            'returnUrl' => $request->query('return_url'),
            'formAction' => route('admin.all-order-list.complete-store', $order->order_no),
            'photoUploadUrl' => route('admin.all-order-list.upload-photo', $order->order_no),
            'photoUploadStatusUrl' => route('admin.all-order-list.photo-upload-status', $order->order_no),
        ]);
    }

    public function completeStore(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'hq'], true), 403);

        $validated = $request->validate([
            'completed_date' => ['required', 'date'],
            'completed_hour' => ['required', 'in:00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23'],
            'completed_minute' => ['required', 'in:00,10,20,30,40,50'],
            'receiver_name' => ['required', 'string', 'max:100'],
            'receiver_relation' => ['nullable', 'string', 'max:50'],
        ], [
            'completed_date.required' => '배송완료일을 입력해 주세요.',
            'completed_hour.required' => '배송완료시간을 선택해 주세요.',
            'completed_hour.in' => '배송완료시간 형식이 올바르지 않습니다.',
            'completed_minute.required' => '배송완료분을 선택해 주세요.',
            'completed_minute.in' => '배송완료분 형식이 올바르지 않습니다.',
            'receiver_name.required' => '인수자를 입력해 주세요.',
        ]);

        if ($order->current_status === 'delivered') {
            throw ValidationException::withMessages([
                'receiver_name' => '이미 배송완료 처리된 주문입니다.',
            ]);
        }

        $deliveredAt = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $validated['completed_date'] . ' ' .
            str_pad((string) $validated['completed_hour'], 2, '0', STR_PAD_LEFT) . ':' .
            $validated['completed_minute'] . ':00'
        );

        DB::transaction(function () use ($order, $user, $validated, $deliveredAt) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->current_status === 'delivered') {
                throw ValidationException::withMessages([
                    'receiver_name' => '이미 배송완료 처리된 주문입니다.',
                ]);
            }

            $fromStatus = $lockedOrder->current_status;

            $lockedOrder->update([
                'current_status' => 'delivered',
                'delivered_at' => $deliveredAt,
                'receiver_name' => $validated['receiver_name'],
                'receiver_relation' => $validated['receiver_relation'] ?? null,
            ]);

            $receiverShop = $lockedOrder->receiver_shop_id
                ? Shop::where('id', $lockedOrder->receiver_shop_id)->lockForUpdate()->first()
                : null;

            OrderHistory::create([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'delivered',
                'message' => '수주사 <strong>' . $this->makeShopDisplayName($receiverShop) . '</strong> 인수정보 업로드 완료',
                'processed_at' => now(),
                'actor_user_id' => $user->id,
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'delivered',
                'changed_by_user_id' => $user->id,
                'memo' => '배송완료 및 인수정보 등록',
                'created_at' => now(),
            ]);

            $pointAmount = (int) $lockedOrder->point_earned_amount;

            if ($pointAmount <= 0) {
                $pointAmount = (int) $lockedOrder->order_amount;

                if ($pointAmount > 0) {
                    $lockedOrder->update([
                        'point_earned_amount' => $pointAmount,
                    ]);
                }
            }

            if ($receiverShop && $pointAmount > 0) {
                $alreadyCredited = DB::table('point_transactions')
                    ->where('order_id', $lockedOrder->id)
                    ->where('shop_id', $receiverShop->id)
                    ->where('transaction_type', 'order_credit')
                    ->exists();

                if (!$alreadyCredited) {
                    $beforeBalance = (int) $receiverShop->current_point_balance;
                    $afterBalance = $beforeBalance + $pointAmount;

                    $receiverShop->update([
                        'current_point_balance' => $afterBalance,
                    ]);

                    DB::table('point_transactions')->insert([
                        'shop_id' => $receiverShop->id,
                        'user_id' => $user->id,
                        'order_id' => $lockedOrder->id,
                        'payment_transaction_id' => null,
                        'transaction_no' => $this->generatePointTransactionNo(),
                        'transaction_type' => 'order_credit',
                        'direction' => 'in',
                        'amount' => $pointAmount,
                        'balance_before' => $beforeBalance,
                        'balance_after' => $afterBalance,
                        'summary' => '배송완료 포인트 적립',
                        'description' => '주문번호 ' . $lockedOrder->order_no . ' 배송완료 처리',
                        'transacted_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return response()->view('pages.popup-complete-result', [
            'message' => '상품 배송이 완료되었습니다.',
            'redirectParentTo' => route('admin.all-order-list'),
            'redirectCurrentTo' => route('admin.all-order-list.popup', $order->order_no),
        ]);
    }

    public function uploadPhoto(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'hq'], true), 403);

        $validated = $request->validate([
            'photo_field' => ['required', 'in:photo_shop,photo_site,photo_extra'],
            'photo_file' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml,application/pdf'],
        ], [
            'photo_file.required' => '업로드할 사진을 선택해 주세요.',
            'photo_file.mimetypes' => '이미지 또는 PDF 파일만 업로드할 수 있습니다.',
        ]);

        $field = $validated['photo_field'];

        $metaMap = [
            'photo_shop' => ['type' => 'other', 'sort' => 1],
            'photo_site' => ['type' => 'delivery_site', 'sort' => 2],
            'photo_extra' => ['type' => 'other', 'sort' => 3],
        ];

        $meta = $metaMap[$field];

        $temp = $this->uploadService->storeTempUpload(
            $request->file('photo_file'),
            Cafe24FileUploadService::TYPE_PHOTO_SHARE
        );

        $task = UploadTask::create([
            'task_type' => 'order_photo',
            'upload_type' => Cafe24FileUploadService::TYPE_PHOTO_SHARE,
            'status' => 'pending',
            'local_path' => $temp['local_path'],
            'original_name' => $temp['original_name'],
            'original_mime_type' => $temp['original_mime_type'],
            'order_id' => $order->id,
            'shop_id' => $order->receiver_shop_id,
            'user_id' => $user->id,
            'photo_field' => $field,
            'photo_type' => $meta['type'],
            'sort_order' => $meta['sort'],
        ]);

        ProcessCafe24UploadJob::dispatch($task->id);

        return response()->json([
            'success' => true,
            'queued' => true,
            'task_id' => $task->id,
            'photo_field' => $field,
            'message' => '성공적으로 사진 업로드가 되었습니다.',
        ]);
    }

    public function photoUploadStatus(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'hq'], true), 403);

        $photos = DB::table('order_photos')
            ->where('order_id', $order->id)
            ->orderBy('sort_order')
            ->get();

        $photoShop = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 1;
        });

        $photoSite = $photos->first(function ($photo) {
            return $photo->photo_type === 'delivery_site';
        });

        $photoExtra = $photos->first(function ($photo) {
            return $photo->photo_type === 'other' && (int) $photo->sort_order === 3;
        });

        $pendingTasks = UploadTask::query()
            ->where('task_type', 'order_photo')
            ->where('order_id', $order->id)
            ->whereIn('status', ['pending', 'processing'])
            ->pluck('photo_field')
            ->all();

        return response()->json([
            'success' => true,
            'photos' => [
                'photo_shop' => $photoShop?->file_path,
                'photo_site' => $photoSite?->file_path,
                'photo_extra' => $photoExtra?->file_path,
            ],
            'pending_fields' => $pendingTasks,
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        $adminUser = $request->user();
        abort_unless($adminUser && in_array($adminUser->role, ['admin', 'hq'], true), 403);

        DB::transaction(function () use ($order, $adminUser) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->current_status === 'cancelled') {
                throw ValidationException::withMessages([
                    'order' => '이미 주문취소 처리된 주문입니다.',
                ]);
            }

            $fromStatus = $lockedOrder->current_status;

            $ordererShop = $lockedOrder->orderer_shop_id
                ? Shop::where('id', $lockedOrder->orderer_shop_id)->lockForUpdate()->first()
                : null;

            $receiverShop = $lockedOrder->receiver_shop_id
                ? Shop::where('id', $lockedOrder->receiver_shop_id)->lockForUpdate()->first()
                : null;

            $refundAmount = (int) ($lockedOrder->payment_amount ?? $lockedOrder->order_amount ?? 0);

            if ($refundAmount < 0) {
                $refundAmount = 0;
            }

            // 1) 발주사 포인트 환불
            if ($ordererShop && $refundAmount > 0) {
                $alreadyRefunded = DB::table('point_transactions')
                    ->where('order_id', $lockedOrder->id)
                    ->where('shop_id', $ordererShop->id)
                    ->where('transaction_type', 'order_cancel_refund')
                    ->exists();

                if (!$alreadyRefunded) {
                    $beforeBalance = (int) $ordererShop->current_point_balance;
                    $afterBalance = $beforeBalance + $refundAmount;

                    $ordererShop->update([
                        'current_point_balance' => $afterBalance,
                    ]);

                    DB::table('point_transactions')->insert([
                        'shop_id' => $ordererShop->id,
                        'user_id' => $adminUser->id,
                        'order_id' => $lockedOrder->id,
                        'payment_transaction_id' => null,
                        'transaction_no' => $this->generatePointTransactionNo(),
                        'transaction_type' => 'order_cancel_refund',
                        'direction' => 'in',
                        'amount' => $refundAmount,
                        'balance_before' => $beforeBalance,
                        'balance_after' => $afterBalance,
                        'summary' => '주문취소 포인트 환불',
                        'description' => '주문번호 ' . $lockedOrder->order_no . ' 발주사 환불',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 2) 배송완료건이면 수주사 포인트 회수
            if ($lockedOrder->current_status === 'delivered' && $receiverShop) {
                $creditedAmount = (int) ($lockedOrder->point_earned_amount ?? 0);

                if ($creditedAmount <= 0) {
                    $creditedAmount = (int) ($lockedOrder->order_amount ?? 0);
                }

                if ($creditedAmount > 0) {
                    $alreadyReversed = DB::table('point_transactions')
                        ->where('order_id', $lockedOrder->id)
                        ->where('shop_id', $receiverShop->id)
                        ->where('transaction_type', 'order_credit_cancel')
                        ->exists();

                    if (!$alreadyReversed) {
                        $beforeBalance = (int) $receiverShop->current_point_balance;
                        $afterBalance = max(0, $beforeBalance - $creditedAmount);

                        $receiverShop->update([
                            'current_point_balance' => $afterBalance,
                        ]);

                        DB::table('point_transactions')->insert([
                            'shop_id' => $receiverShop->id,
                            'user_id' => $adminUser->id,
                            'order_id' => $lockedOrder->id,
                            'payment_transaction_id' => null,
                            'transaction_no' => $this->generatePointTransactionNo(),
                            'transaction_type' => 'order_credit_cancel',
                            'direction' => 'out',
                            'amount' => $creditedAmount,
                            'balance_before' => $beforeBalance,
                            'balance_after' => $afterBalance,
                            'summary' => '주문취소 포인트 회수',
                            'description' => '주문번호 ' . $lockedOrder->order_no . ' 수주사 회수',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 3) 주문 상태 취소 처리
            $lockedOrder->update([
                'current_status' => 'cancelled',
            ]);

            OrderHistory::create([
                'order_id' => $lockedOrder->id,
                'order_no' => $lockedOrder->order_no,
                'history_type' => 'cancelled',
                'message' => '<strong>본부 수발주사업부</strong> 에서 주문취소 처리',
                'processed_at' => now(),
                'actor_user_id' => $adminUser->id,
            ]);

            DB::table('order_status_logs')->insert([
                'order_id' => $lockedOrder->id,
                'from_status' => $fromStatus,
                'to_status' => 'cancelled',
                'changed_by_user_id' => $adminUser->id,
                'memo' => '관리자 주문취소 처리',
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '주문취소 처리되었습니다.',
        ]);
    }

    public function hide(Request $request, Order $order)
    {
        $adminUser = $request->user();

        abort_unless($adminUser && in_array($adminUser->role, ['admin', 'hq'], true), 403);

        $affected = DB::table('orders')
            ->where('id', $order->id)
            ->where('is_hidden', 0)
            ->update([
                'is_hidden' => 1,
                'hidden_at' => now(),
                'hidden_by_admin_user_id' => $adminUser->id,
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            return response()->json([
                'success' => false,
                'message' => '이미 삭제 처리되었거나 숨김 처리에 실패했습니다.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => '주문서가 삭제 처리되었습니다.',
        ]);
    }

    protected function makeShopDisplayName(?Shop $shop): string
    {
        if (!$shop) {
            return '미지정 화원';
        }

        if ((int) $shop->id === 1) {
            return '본부 수발주사업부';
        }

        $regionLabel = DB::table('shop_delivery_areas')
            ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
            ->where('shop_delivery_areas.shop_id', $shop->id)
            ->orderBy('shop_delivery_areas.id')
            ->value('regions.sido');

        return $shop->shop_name . ($regionLabel ? ' (' . $regionLabel . ')' : '');
    }

    protected function generatePointTransactionNo(): string
    {
        do {
            $transactionNo = 'PT' . now()->format('YmdHis') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (DB::table('point_transactions')->where('transaction_no', $transactionNo)->exists());

        return $transactionNo;
    }
}
