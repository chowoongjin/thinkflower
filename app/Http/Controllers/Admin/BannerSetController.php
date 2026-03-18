<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\Cafe24FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerSetController extends Controller
{
    protected Cafe24FileUploadService $uploadService;

    public function __construct(Cafe24FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index()
    {
        $mainBanners = Banner::query()
            ->where('banner_type', 'main')
            ->orderBy('sort_order')
            ->get()
            ->keyBy('sort_order');

        $popupBanners = Banner::query()
            ->where('banner_type', 'notice')
            ->orderBy('sort_order')
            ->get()
            ->keyBy('sort_order');

        return view('admin.banner-set', compact('mainBanners', 'popupBanners'));
    }

    public function store(Request $request)
    {
        $rules = [];

        for ($i = 1; $i <= 8; $i++) {
            $rules["main_banner_files.$i"] = ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml,application/pdf'];
            $rules["main_banner_delete.$i"] = ['nullable', 'in:0,1'];
        }

        for ($i = 1; $i <= 6; $i++) {
            $rules["popup_banner_files.$i"] = ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml,application/pdf'];
            $rules["popup_banner_delete.$i"] = ['nullable', 'in:0,1'];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request) {
            $this->syncBannerGroup(
                $request,
                'main',
                8,
                'main_banner_files',
                'main_banner_delete',
                Cafe24FileUploadService::TYPE_BANNER_NOTICE
            );

            $this->syncBannerGroup(
                $request,
                'notice',
                6,
                'popup_banner_files',
                'popup_banner_delete',
                Cafe24FileUploadService::TYPE_BANNER_POPUP
            );
        });

        return redirect()
            ->route('admin.banner-set.index')
            ->with('success', '저장되었습니다.');
    }

    protected function syncBannerGroup(
        Request $request,
        string $bannerType,
        int $maxSlot,
        string $fileInputName,
        string $deleteInputName,
        string $uploadType
    ): void {
        $uploadedFiles = $request->file($fileInputName, []);

        for ($slot = 1; $slot <= $maxSlot; $slot++) {
            $banner = Banner::query()->firstOrCreate(
                [
                    'banner_type' => $bannerType,
                    'sort_order' => $slot,
                ],
                [
                    'title' => null,
                    'content' => null,
                    'image_path' => null,
                    'link_url' => null,
                    'is_active' => 1,
                    'start_at' => null,
                    'end_at' => null,
                ]
            );

            $deleteFlag = (string) $request->input($deleteInputName . '.' . $slot, '0') === '1';
            $uploadedFile = $uploadedFiles[$slot] ?? null;

            if ($deleteFlag) {
                $banner->update([
                    'image_path' => null,
                    'is_active' => 0,
                ]);
            }

            if ($uploadedFile) {
                $uploaded = $this->uploadService->upload($uploadedFile, $uploadType);

                $banner->update([
                    'image_path' => $uploaded['url'],
                    'is_active' => 1,
                ]);
            }
        }
    }
}
