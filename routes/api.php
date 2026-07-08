<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/device', [ApiController::class, 'authDevice']);
Route::post('/license/check', [ApiController::class, 'checkLicense']);
Route::post('/license/activate', [ApiController::class, 'activateLicense']);
Route::post('/heartbeat', [ApiController::class, 'heartbeat']);
Route::post('/version', [ApiController::class, 'checkVersion']);
Route::post('/payment/create', [ApiController::class, 'createPayment']);
Route::post('/payment/webhook', [ApiController::class, 'webhook']);
Route::post('/log', [ApiController::class, 'uploadLog']);
Route::get('/settings', [ApiController::class, 'getSettings']);
