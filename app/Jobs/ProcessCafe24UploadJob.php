<?php

namespace App\Jobs;

use App\Models\UploadTask;
use App\Services\Cafe24FileUploadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessCafe24UploadJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public int $uploadTaskId)
    {
    }

    public function handle(Cafe24FileUploadService $uploadService): void
    {
        $task = UploadTask::query()->findOrFail($this->uploadTaskId);

        if ($task->status === 'done') {
            return;
        }

        $task->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        try {
            $uploaded = $uploadService->uploadFromLocalPath(
                localPath: $task->local_path,
                type: $task->upload_type,
                originalName: $task->original_name,
                originalMimeType: $task->original_mime_type,
            );

            DB::transaction(function () use ($task, $uploaded) {
                $task->update([
                    'status' => 'done',
                    'disk' => $uploaded['disk'],
                    'mime_type' => $uploaded['mime_type'],
                    'relative_path' => $uploaded['relative_path'],
                    'url' => $uploaded['url'],
                    'processed_at' => now(),
                ]);

                if ($task->task_type === 'order_photo' && $task->order_id) {
                    DB::table('order_photos')
                        ->where('order_id', $task->order_id)
                        ->where('photo_type', $task->photo_type)
                        ->where('sort_order', $task->sort_order)
                        ->delete();

                    DB::table('order_photos')->insert([
                        'order_id' => $task->order_id,
                        'photo_type' => $task->photo_type,
                        'file_path' => $uploaded['url'],
                        'sort_order' => $task->sort_order,
                        'uploaded_by_user_id' => $task->user_id,
                        'created_at' => now(),
                    ]);

                    $orderNo = DB::table('orders')->where('id', $task->order_id)->value('order_no');

                    $photoLabelMap = [
                        'photo_shop' => '매장사진',
                        'photo_site' => '현장사진',
                        'photo_extra' => '추가사진',
                    ];

                    $photoLabel = $photoLabelMap[$task->photo_field] ?? '사진';

                    $shop = DB::table('shops')->where('id', $task->shop_id)->first();

                    $regionLabel = null;
                    if ($shop) {
                        $regionLabel = DB::table('shop_delivery_areas')
                            ->join('regions', 'shop_delivery_areas.region_id', '=', 'regions.id')
                            ->where('shop_delivery_areas.shop_id', $shop->id)
                            ->orderBy('shop_delivery_areas.id')
                            ->value('regions.sido');
                    }

                    $shopDisplayName = $shop
                        ? $shop->shop_name . ($regionLabel ? ' (' . $regionLabel . ')' : '')
                        : '수주사';

                    DB::table('order_histories')->insert([
                        'order_id' => $task->order_id,
                        'order_no' => $orderNo,
                        'history_type' => 'updated',
                        'message' => '수주사 <strong>' . $shopDisplayName . '</strong> ' . $photoLabel . ' 업로드 완료',
                        'processed_at' => now(),
                        'actor_user_id' => $task->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
        } catch (Throwable $e) {
            $task->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
