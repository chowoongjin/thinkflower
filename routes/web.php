<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderListController;
use App\Http\Controllers\SujuListController;
use App\Http\Controllers\CalculateListController;
use App\Http\Controllers\PhotoListController;
use App\Http\Controllers\Admin\RealTimeController;
use App\Http\Controllers\Admin\AllOrderListController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\Admin\MediationListController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\MemberListController;
use App\Http\Controllers\Admin\CalculateListController as AdminCalculateListController;
use App\Http\Controllers\Admin\BannerSetController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BillListController;
use App\Http\Controllers\UsageFeeController;

use App\Http\Controllers\PointChargeController; //테스트

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Models\Notice;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/forgot-password', function () {
        return '비밀번호 찾기 페이지 준비중';
    })->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/bonbu-balju', function () {
        $now = now();

        $generalNotices = Notice::query()
            ->where('category', 'general')
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $specialNotices = Notice::query()
            ->where('category', 'special')
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('pages.bonbu-balju', compact('generalNotices', 'specialNotices'));
    })->name('bonbu-balju');

    Route::get('/modal/point-charge', [ModalController::class, 'pointCharge'])->name('modal.point-charge');
    Route::post('/point-charge/test', [PointChargeController::class, 'store'])->name('point-charge.test.store');
    Route::post('/bonbu-balju/order', [OrderController::class, 'store'])->name('bonbu-balju.order.store');
    Route::get('/order-list', [OrderListController::class, 'index'])->name('order-list');
    Route::get('/order-list/{order}/popup', [OrderListController::class, 'popup'])->name('order-list.popup');
    Route::get('/order-list/{order}/history-modal', [OrderListController::class, 'historyModal'])->name('order-list.history-modal');
    Route::get('/order-list/{order}/photo-popup', [OrderListController::class, 'photoPopup'])->name('order-list.photo-popup');

    Route::get('/suju-list', [SujuListController::class, 'index'])->name('suju-list');
    Route::get('/suju-list/{order}/popup', [SujuListController::class, 'popup'])->name('suju-list.popup');
    Route::get('/suju-list/{order}/history-modal', [SujuListController::class, 'historyModal'])->name('suju-list.history-modal');
    Route::post('/suju-list/{order}/change-status', [SujuListController::class, 'changeStatusFromList'])->name('suju-list.change-status');

    Route::post('/suju-list/{order}/accept', [SujuListController::class, 'accept'])->name('suju-list.accept');
    Route::post('/suju-list/{order}/reject', [SujuListController::class, 'reject'])->name('suju-list.reject');

    Route::get('/suju-list/{order:order_no}/complete-popup', [SujuListController::class, 'completePopup'])->name('suju-list.complete-popup');
    Route::post('/suju-list/{order:order_no}/complete', [SujuListController::class, 'completeStore'])->name('suju-list.complete-store');
    Route::post('/suju-list/{order:order_no}/upload-photo', [SujuListController::class, 'uploadPhoto'])->name('suju-list.upload-photo');
    Route::get('/suju-list/{order:order_no}/photo-upload-status', [SujuListController::class, 'photoUploadStatus'])->name('suju-list.photo-upload-status');
    Route::get('/suju-list/{order}/photo-popup', [SujuListController::class, 'photoPopup'])->name('suju-list.photo-popup');

    Route::get('/calculate-list', [CalculateListController::class, 'index'])->name('calculate-list');
    Route::get('/photo-list', [PhotoListController::class, 'index'])->name('photo-list');

    Route::get('/my-page', [MyPageController::class, 'show'])->name('my-page.show');
    Route::get('/my-page/business-info-modal', [MyPageController::class, 'businessInfoModal'])->name('my-page.business-info-modal');
    Route::post('/my-page/business-info', [MyPageController::class, 'updateBusinessInfo'])->name('my-page.business-info.update');
    Route::get('/my-page/shop-info-modal', [MyPageController::class, 'shopInfoModal'])->name('my-page.shop-info-modal');
    Route::post('/my-page/shop-info', [MyPageController::class, 'updateShopInfo'])->name('my-page.shop-info.update');
    Route::get('/my-page/settlement-info-modal', [MyPageController::class, 'settlementInfoModal'])->name('my-page.settlement-info-modal');
    Route::post('/my-page/settlement-info', [MyPageController::class, 'updateSettlementInfo'])->name('my-page.settlement-info.update');

    Route::get('/announcement', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('/announcement/{announcement}', [AnnouncementController::class, 'show'])->name('announcement.show');
    Route::get('/usage-fee', [UsageFeeController::class, 'index'])->name('usage-fee');
    Route::get('/bill-list', [BillListController::class, 'index'])->name('bill-list');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/modal/products', [ModalController::class, 'products'])->name('modal.products');
    Route::get('/modal/regions', [ModalController::class, 'regionsStep1'])->name('modal.regions.step1');
    Route::get('/modal/regions/detail', [ModalController::class, 'regionsStep2'])->name('modal.regions.step2');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminHomeController::class, 'index'])->name('index');

    Route::get('/real-time', [RealTimeController::class, 'index'])->name('real-time.index');
    Route::post('/real-time', [RealTimeController::class, 'store'])->name('real-time.store');
    Route::get('/member-list-popup', [RealTimeController::class, 'memberListPopup'])->name('member-list-popup');

    Route::get('/all-order-list', [AllOrderListController::class, 'index'])->name('all-order-list');
    Route::get('/all-order-list/{order}/popup', [AllOrderListController::class, 'popup'])->name('all-order-list.popup');
    Route::get('/all-order-list/{order}/history-modal', [AllOrderListController::class, 'historyModal'])->name('all-order-list.history-modal');
    Route::get('/all-order-list/{order}/photo-popup', [AllOrderListController::class, 'photoPopup'])->name('all-order-list.photo-popup');
    Route::post('/all-order-list/{order}/assign-receiver', [AllOrderListController::class, 'assignReceiver'])->name('all-order-list.assign-receiver');
    Route::post('/all-order-list/{order}/accept', [AllOrderListController::class, 'accept'])->name('all-order-list.accept');
    Route::post('/all-order-list/{order}/reset-brokerage', [AllOrderListController::class, 'resetBrokerage'])->name('all-order-list.reset-brokerage');
    Route::get('/all-order-list/{order:order_no}/complete-popup', [AllOrderListController::class, 'completePopup'])->name('all-order-list.complete-popup');
    Route::post('/all-order-list/{order:order_no}/complete', [AllOrderListController::class, 'completeStore'])->name('all-order-list.complete-store');
    Route::post('/all-order-list/{order:order_no}/upload-photo', [AllOrderListController::class, 'uploadPhoto'])->name('all-order-list.upload-photo');
    Route::get('/all-order-list/{order:order_no}/photo-upload-status', [AllOrderListController::class, 'photoUploadStatus'])->name('all-order-list.photo-upload-status');
    Route::post('/all-order-list/{order:order_no}/cancel', [AllOrderListController::class, 'cancel'])->name('all-order-list.cancel');
    Route::post('/all-order-list/{order:order_no}/hide', [AllOrderListController::class, 'hide'])->name('all-order-list.hide');

    Route::get('/mediation-list', [MediationListController::class, 'index'])->name('mediation-list');
    Route::get('/mediation-list/{order}/popup', [MediationListController::class, 'popup'])->name('mediation-list.popup');
    Route::get('/mediation-list/{order}/receiver-popup', [MediationListController::class, 'receiverPopup'])->name('mediation-list.receiver-popup');
    Route::post('/mediation-list/{order}/assign-receiver', [MediationListController::class, 'assignReceiver'])->name('mediation-list.assign-receiver');

    Route::get('/notice', [NoticeController::class, 'index'])->name('notice.index');
    Route::post('/notice', [NoticeController::class, 'store'])->name('notice.store');

    Route::get('/calculate-list', [AdminCalculateListController::class, 'index'])->name('calculate-list');

    Route::get('/banner-set', [BannerSetController::class, 'index'])->name('banner-set.index');
    Route::post('/banner-set', [BannerSetController::class, 'store'])->name('banner-set.store');


    Route::get('/member-list', [MemberListController::class, 'index'])->name('member-list');
});
