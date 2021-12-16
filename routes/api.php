<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use GuzzleHttp\Client;

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

Route::post('/create',[UserController::class,'create']);
Route::post('/login',[UserController::class,'login']);


// Route::group(['middleware' => 'auth'], function () {
//     Route::get('/test',[UserController::class,'test']);

// });

// Route::middleware('auth:sanctum')->get('/test', function (Request $request) {
//     return 'ici';
// });

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/test',[UserController::class,'test']);
    Route::post('/upload',[FileController::class,'createFile']);
    Route::post('/yousign/{id}',[FileController::class,'uploadFile']);
    Route::post('/yousignurl/{id}',[FileController::class,'secondprocess']);
    Route::post('/members/{id}',[FileController::class,'getMembers']);
});
//Route::get('/test',[UserController::class,'test']);