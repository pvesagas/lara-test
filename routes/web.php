<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('store');
});

Route::group(['prefix' => 'product'], function() {
    Route::get('/', function () {
        return view('product');
    });
});

Route::group(['prefix' => 'cart'], function() {
   Route::get('/', function () {
       return view('cart');
   });
   Route::get('/items', [CartController::class, 'getCart']);
   Route::post('/add', [CartController::class, 'addToCart']);
   Route::put('/', [CartController::class, 'updateCart']);
   Route::delete('/', [CartController::class, 'removeCartItem']);
   Route::get('/clear', [CartController::class, 'clearCart']);
});

Route::post('imageUpload', [FileUploadController::class, 'uploadImage']);
