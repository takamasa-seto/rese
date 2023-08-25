<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ShopController::class, 'index']);
Route::post('/favorite', [FavoriteController::class, 'flip']);
Route::get('/detail/{shop_id}', [ShopController::class, 'detail']);
Route::post('/reserve', [ReserveController::class, 'store'])->middleware(['verified']);
Route::get('/reserve/edit', [ReserveController::class, 'edit'])->middleware(['verified']);
Route::post('/reserve/update', [ReserveController::class, 'update'])->middleware(['verified']);
Route::post('/reserve/delete', [ReserveController::class, 'destroy'])->middleware(['verified']);
Route::get('/reserve/cancel', [ReserveController::class, 'showCancel'])->middleware(['verified']);
Route::get('/my_page', [MyPageController::class, 'create'])->middleware(['verified']);
Route::get('/qr_code', [MyPageController::class, 'showQrCode'])->middleware(['verified']);
Route::get('/feedback/{reservation_id}', [FeedbackController::class, 'create'])->middleware(['verified']);
Route::post('/feedback/store', [FeedbackController::class, 'store'])->middleware(['verified']);

//Review機能の追加
Route::get('/review/add/{shop_id}', [ReviewController::class, 'create'])->middleware(['verified']);
Route::post('/review/store', [ReviewController::class, 'store'])->middleware(['verified']);
Route::get('/review/shop_index/{shop_id}', [ReviewController::class, 'shopIndex']);
Route::post('/review/delete', [ReviewController::class, 'destroy']);
Route::get('/review/edit/{shop_id}', [ReviewController::class, 'edit'])->middleware(['verified']);
Route::post('/review/update', [ReviewController::class, 'update'])->middleware(['verified']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/index', [AdminController::class, 'index'])->middleware(['auth:admin']);
    Route::post('/add', [AdminController::class, 'store'])->middleware(['auth:admin']);
    Route::post('/delete', [AdminController::class, 'destroy'])->middleware(['auth:admin']);
    Route::get('/reservations', [MaintenanceController::class, 'showReservations'])->middleware(['auth:admin']);
    Route::get('/reservations/detail/{reservation_id}', [MaintenanceController::class, 'detail'])->middleware(['auth:admin']);
    Route::get('/edit', [MaintenanceController::class, 'edit'])->middleware(['auth:admin']);
    Route::post('/shop_update', [ShopController::class, 'update'])->middleware(['auth:admin']);
    Route::get('/new_shop', [MaintenanceController::class, 'add'])->middleware(['auth:admin']);
    Route::post('/shop_add', [ShopController::class, 'store'])->middleware(['auth:admin']);
    Route::get('/make_announcement', [AdminController::class, 'makeAnnouncement'])->middleware(['auth:admin']);
    Route::post('/send', [AdminController::class, 'send'])->middleware(['auth:admin']);

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->middleware(['auth:admin'])->name('dashboard');

    //Review機能の追加
    Route::get('/review/manager', [ReviewController::class, 'adminIndex'])->middleware(['auth:admin']);
    //CSVインポート機能の追加
    Route::get('/shop_csv_importer', [ShopController::class, 'createCsvImporter'])->middleware(['auth:admin']);
    Route::post('/store_from_csv', [ShopController::class, 'storeFromCsv'])->middleware(['auth:admin']);

    require __DIR__.'/admin.php';
});
