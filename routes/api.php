<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/signout', [AuthController::class, 'signOut']);
});

Route::prefix('webhooks')->group(function () {
    Route::post('/orders', [WebhookController::class, 'orderCreated']);
    Route::post('/items', [WebhookController::class, 'itemCreated']);
    Route::post('/app/uninstall', [WebhookController::class, 'appUninstalled']);
});


// GDPR Endpoints
Route::post('/webhooks/customer_data', [WebhookController::class, 'returnCustomerData']);
Route::post('/webhooks/customer_data_delete', [WebhookController::class, 'deleteCustomerData']);
Route::post('/webhooks/shop_data_delete', [WebhookController::class, 'deleteShopData']);

// Auth 
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
