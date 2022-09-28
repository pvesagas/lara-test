<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'product'], function() {
    Route::get('/', [ProductController::class, 'getAllProduct']);
    Route::get('/{id}', [ProductController::class, 'getProduct']);
    Route::post('/', [ProductController::class, 'storeProduct']);
    Route::put('/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('/{id}', [ProductController::class, 'deleteProduct']);
});

Route::group(['prefix' => 'category'], function() {
    Route::get('/', [CategoryController::class, 'getAllCategory']);
    Route::get('/{id}', [CategoryController::class, 'getCategory']);
    Route::post('/', [CategoryController::class, 'storeCategory']);
    Route::put('/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/{id}', [CategoryController::class, 'deleteCategory']);
});
