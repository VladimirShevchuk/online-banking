<?php

use App\Http\Controllers\AccountStatementController;
use App\Http\Controllers\BatchTransferController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware(['auth.basic'])->group(function () {
    Route::post('/transfers', [TransferController::class, 'store']);
    Route::get('/transfers', [TransferController::class, 'index']);
    Route::get('/transfers/{id}', [TransferController::class, 'show']);
    Route::post('/transfers/batch', [BatchTransferController::class, 'store']);
    Route::get('/transfers/batch/{id}', [BatchTransferController::class, 'show']);
    Route::get('/accounts/statement', [AccountStatementController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
});
