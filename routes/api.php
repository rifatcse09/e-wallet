<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TransactionController;
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

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user-profile', [UserController::class, 'userProfile']);
    Route::get('/convert-currency', [TransactionController::class, 'currencyApiCheck']);
    Route::post('/send-money', [TransactionController::class, 'sendMoney']);
    Route::get('/user-transaction-info', [TransactionController::class, 'userTransactionInfo']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
