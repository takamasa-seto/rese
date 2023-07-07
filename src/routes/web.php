<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaintenanceController;

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
Route::post('/reserve', [ReserveController::class, 'store'])->middleware(['auth']);
Route::post('/reserve/delete', [ReserveController::class, 'destroy'])->middleware(['auth']);
Route::get('/reserve/cancel', [ReserveController::class, 'showCancel'])->middleware(['auth']);
Route::get('/my_page', [MyPageController::class, 'create'])->middleware(['auth']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/index', [AdminController::class, 'index'])->middleware(['auth:admin']);
    Route::post('/add', [AdminController::class, 'store'])->middleware(['auth:admin']);
    Route::post('/delete', [AdminController::class, 'destroy'])->middleware(['auth:admin']);
    Route::get('/reservations', [MaintenanceController::class, 'showReservations'])->middleware(['auth:admin']);
    Route::get('/edit', [MaintenanceController::class, 'edit'])->middleware(['auth:admin']);
    Route::post('/shop_update', [MaintenanceController::class, 'update'])->middleware(['auth:admin']);
    Route::get('/make_announcement', [AdminController::class, 'makeAnnouncement'])->middleware(['auth:admin']);
    Route::post('/send', [AdminController::class, 'send'])->middleware(['auth:admin']);

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->middleware(['auth:admin'])->name('dashboard');

    require __DIR__.'/admin.php';
});
