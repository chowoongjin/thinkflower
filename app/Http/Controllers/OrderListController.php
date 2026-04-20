<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCafe24UploadJob;
use App\Models\Order;
use App\Models\UploadTask;
use App\Services\Cafe24FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderListController extends Controller
{
    protected Cafe24FileUploadService $uploadService;

    public function __construct(Cafe24FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : Carbon::today()->subDays(15)->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : Carbon::today()->addDays(15)->format('Y-m-d');

        $query = Order::query()
            ->with(['ordererShop', 'receiverShop'])
            ->withCount(['uploadedPhotos as photos_count'])
            ->where('created_user_id', $user->id)
            ->where('is_hidden', 0)
            ->whereDate('delivery_date', '>=', $dateFrom)
            ->whereDate('delivery_date', '<=', $dateTo);

        if ($request->filled('product_name')) {
            $productName = trim($request->product_name);

            if ($productName === '근조화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '근조3단%')
                        ->orWhere('product_name', 'like', '근조화환%');
                });
            } elseif ($productName === '축하화환') {
                $query->where(function ($q) {
                    $q->where('product_name', 'like', '축하3단%')
                        ->orWhere('product_name', 'like', '축하화환%');
                });
            } else {
                $query->where('product_name', 'like', '%' . $productName . '%');
            }
        }

        if ($request->filled('order_no')) {
            $query->where('order_no', 'like', '%' . trim($request->order_no) . '%');
        }

        if ($request->filled('delivery_addr1')) {
            $query->where('delivery_addr1', 'like', '%' . trim($request->delivery_addr1) . '%');
        }

        if ($request->filled('recipient_name')) {
            $query->where('recipient_name', 'like', '%' . trim($request->recipient_name) . '%');
        }

        $orders = (clone $query)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $summaryCount = (clone $query)->count();
        $summaryAmount = (clone $query)->sum('payment_amount');

        $isEasyView = $request->boolean('easy_view');

        $data = compact(
            'orders',
            'summaryCount',
            'summaryAmount',
            'dateFrom',
            'dateTo',
            'isEasyView'
        );

        if ($request->ajax()) {
            return view(
                $isEasyView
                    ? 'pages.partials.order-list-easy-table'
                    : 'pages.partials.order-list-table',
                $data
            );
        }

        return view('pages.order-list', $data);
    }

    public function popup(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);
        abort_if((int) $order->is_hidden === 1, 404);

        $order->load([
            'ordererShop',
            'receiverShop',
        ]);

        return view('pages.order-popup', [
            'order' => $order,
            'title' => '주문정보',
        ]);
    }

    public function historyModal(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);
        abort_if((int) $order->is_hidden === 1, 404);

        $histories = $order->histories()
            ->orderBy('processed_at')
            ->orderBy('id')
            ->get();

        return view('pages.partials.order-history-modal', [
            'order' => $order,
            'histories' => $histories,
        ]);
    }

    public function completePopup(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);
        abort_if((int) $order->is_hidden === 1, 404);

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
            'formAction' => route('order-list.complete-store', $order->order_no),
            'photoUploadUrl' => route('order-list.upload-photo', $order->order_no),
            'photoUploadStatusUrl' => route('order-list.photo-upload-status', $order->order_no),
            'allowCompleteAction' => false,
        ]);
    }

    public function completeStore(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless($user && $order->created_user_id === $user->id, 403);
        abort_if((int) $order->is_hidden === 1, 404);
        
        return response()->view('pages.popup-action-result', [
            'message' => '배송완료 처리는 수주화원만 가능합니다.',
            'redirectCurrentTo' => route('order-list.complete-popup', $order->order_no),
        ]);
    }

    public function uploadPhoto(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless($user && $order->created_user_id === $user->id, 403);
        abort_if((int) $order->is_hidden === 1, 404);

        $validated = $request->validate([
            'photo_field' => ['required', 'in:photo_shop,photo_site,photo_extra'],
            'photo_file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,bmp,svg', 'max:20480'],
        ], [
            'photo_field.required' => '업로드할 사진 유형을 선택해 주세요.',
            'photo_field.in' => '업로드할 수 없는 사진 유형입니다.',
            'photo_file.required' => '업로드할 파일을 선택해 주세요.',
            'photo_file.file' => '업로드할 파일 형식이 올바르지 않습니다.',
            'photo_file.mimes' => '이미지 파일만 업로드할 수 있습니다.',
            'photo_file.max' => '파일 크기는 20MB 이하여야 합니다.',
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
            'shop_id' => $order->orderer_shop_id,
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

        abort_unless($user && $order->created_user_id === $user->id, 403);
        abort_if((int) $order->is_hidden === 1, 404);

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

    public function photoPopup(Request $request, Order $order)
    {
        abort_unless($order->created_user_id === $request->user()->id, 403);
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
}
