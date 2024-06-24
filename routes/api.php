<?php

use App\Http\Controllers\Api\FaceswapController;
use App\Http\Controllers\Api\ModelImageController;
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

Route::post('faceswap', [FaceswapController::class, 'faceswap']);
Route::get('store-themes', [ModelImageController::class, 'storeThemes']);
Route::get('get-themes', [ModelImageController::class, 'getThemes']);
Route::get('store-images', [ModelImageController::class, 'storeImages']);




