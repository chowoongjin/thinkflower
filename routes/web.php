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

    Route::post('/bonbu-balju/order', [OrderController::class, 'store'])->name('bonbu-balju.order.store');
    Route::post('/bonbu-balju/hq', [OrderController::class, 'storeHq'])->name('bonbu-balju.hq.store');

    Route::get('/order-list', [OrderListController::class, 'index'])->name('order-list');
    Route::get('/order-list/{order}/popup', [OrderListController::class, 'popup'])->name('order-list.popup');
    Route::get('/order-list/{order}/history-modal', [OrderListController::class, 'historyModal'])->name('order-list.history-modal');
    Route::get('/order-list/{order}/photo-popup', [OrderListController::class, 'photoPopup'])->name('order-list.photo-popup');

    Route::get('/suju-list', [SujuListController::class, 'index'])->name('suju-list');
    Route::get('/suju-list/{order}/popup', [SujuListController::class, 'popup'])->name('suju-list.popup');
    Route::get('/suju-list/{order}/history-modal', [SujuListController::class, 'historyModal'])->name('suju-list.history-modal');

    Route::post('/suju-list/{order}/accept', [SujuListController::class, 'accept'])->name('suju-list.accept');
    Route::post('/suju-list/{order}/reject', [SujuListController::class, 'reject'])->name('suju-list.reject');

    Route::get('/suju-list/{order:order_no}/complete-popup', [SujuListController::class, 'completePopup'])->name('suju-list.complete-popup');
    Route::post('/suju-list/{order:order_no}/complete', [SujuListController::class, 'completeStore'])->name('suju-list.complete-store');
    Route::post('/suju-list/{order:order_no}/upload-photo', [SujuListController::class, 'uploadPhoto'])->name('suju-list.upload-photo');
    Route::get('/suju-list/{order}/photo-popup', [SujuListController::class, 'photoPopup'])->name('suju-list.photo-popup');

    Route::get('/calculate-list', [CalculateListController::class, 'index'])->name('calculate-list');
    Route::get('/photo-list', [PhotoListController::class, 'index'])->name('photo-list');

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
});
