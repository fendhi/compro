<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Webhook Routes (No Auth - untuk callback dari BCA)
|--------------------------------------------------------------------------
*/
Route::post('/webhook/bca-qris', [WebhookController::class, 'handleBCAQRIS']);

/*
|--------------------------------------------------------------------------
| Payment Status Check (dengan Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/check-payment/{orderId}', [WebhookController::class, 'checkPaymentStatus']);
