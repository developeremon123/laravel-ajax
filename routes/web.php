<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/upazila-list', [HomeController::class, 'upazilaList'])->name('upazila.list');
Route::prefix('user')->as('user.')->group(function () {
    Route::post('/store', [HomeController::class, 'store'])->name('store');
    Route::post('/list', [HomeController::class, 'userList'])->name('list');
    Route::post('/edit', [HomeController::class, 'userEdit'])->name('edit');
    Route::post('/show', [HomeController::class, 'userShow'])->name('show');
    Route::post('/delete', [HomeController::class, 'userDelete'])->name('delete');
    Route::post('/change-status', [HomeController::class, 'changeStatus'])->name('change.status');
    Route::post('/bulk-action-delete', [HomeController::class, 'bulkActionDelete'])->name('bulk.action.delete');
});
