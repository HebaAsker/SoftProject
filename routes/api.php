<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ChatMessageController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/logout',[AuthController::class,'logout']);
    Route::post('/auth/update',[AuthController::class,'update']);
    Route::apiResource('/chat', ChatController::class)->only(['index','store','show']);
    Route::apiResource('/chat_message', ChatMessageController::class)->only(['index','store']);
    Route::get('users', [UserController::class,'index']);
});

Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);
