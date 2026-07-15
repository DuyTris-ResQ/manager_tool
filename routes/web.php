<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\VersionController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Redirect homepage to admin dashboard
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Route to clear config cache (handy for shared hosting)
Route::get('/clear-config', function() {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return "Config cache cleared successfully!";
});

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard Routes (Protected by auth and active check)
Route::middleware(['auth', 'active_user'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Licenses
    Route::get('/licenses', [LicenseController::class, 'index'])->name('licenses.index');
    Route::post('/licenses/store', [LicenseController::class, 'store'])->name('licenses.store');
    Route::post('/licenses/{license}/update', [LicenseController::class, 'update'])->name('licenses.update');
    Route::post('/licenses/{license}/extend', [LicenseController::class, 'extend'])->name('licenses.extend');
    Route::post('/licenses/{license}/max-devices', [LicenseController::class, 'changeMaxDevices'])->name('licenses.max_devices');
    Route::post('/licenses/{license}/quick-update', [LicenseController::class, 'quickUpdate'])->name('licenses.quick_update');
    Route::post('/licenses/{license}/delete', [LicenseController::class, 'destroy'])->name('licenses.destroy');
    Route::post('/licenses/bulk-action', [LicenseController::class, 'bulkAction'])->name('licenses.bulk_action');

    // Devices
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices/{device}/remove', [DeviceController::class, 'remove'])->name('devices.remove');
    Route::post('/devices/{device}/block', [DeviceController::class, 'block'])->name('devices.block');
    Route::post('/devices/bulk-action', [DeviceController::class, 'bulkAction'])->name('devices.bulk_action');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Versions
    Route::get('/versions', [VersionController::class, 'index'])->name('versions.index');
    Route::post('/versions/store', [VersionController::class, 'store'])->name('versions.store');
    Route::post('/versions/{version}/delete', [VersionController::class, 'destroy'])->name('versions.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/check-sepay', [SettingController::class, 'checkSepay'])->name('settings.check_sepay');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/details', [\App\Http\Controllers\Admin\UserController::class, 'details'])->name('users.details');
    Route::post('/users/store', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/update', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/delete', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::post('/logs/clear', [LogController::class, 'clear'])->name('logs.clear');
    Route::post('/logs/{log}/delete', [LogController::class, 'destroy'])->name('logs.destroy');
});

// Public Payment Route
Route::get('/payment/pay/{order_code}', function ($order_code) {
    $order = Order::where('order_code', $order_code)->firstOrFail();
    $license = $order->license;
    $gateway = $license ? $license->getSetting('payment_gateway', 'vietqr_only') : 'vietqr_only';

    if ($gateway === 'sepay' && $license) {
        $merchantId = $license->getSetting('sepay_merchant_id', '');
        $apiKey = $license->getSetting('sepay_api_key', '');
        $env = $license->getSetting('sepay_env', 'sandbox');

        // Check if credentials are set
        if (!empty($merchantId) && !empty($apiKey)) {
            try {
                $sepay = new \SePay\SePayClient($merchantId, $apiKey, $env);
                
                $checkoutData = \SePay\Builders\CheckoutBuilder::make()
                    ->paymentMethod('BANK_TRANSFER')
                    ->currency('VND')
                    ->orderInvoiceNumber($order->order_code)
                    ->orderAmount((int) $order->amount)
                    ->operation('PURCHASE')
                    ->orderDescription("Thanh toan don hang {$order->order_code}")
                    ->build();

                return $sepay->checkout()->generateFormHtml($checkoutData);
            } catch (\Exception $e) {
                // Fallback to simulation if initialization fails
            }
        }
    }

    $bank_name = $license ? $license->getSetting('bank_name', 'MBBank') : Setting::get('bank_name', 'MBBank');
    $bank_account = $license ? $license->getSetting('bank_account', '') : Setting::get('bank_account', '');
    $bank_holder = $license ? $license->getSetting('bank_holder', '') : Setting::get('bank_holder', '');
    return view('payment.simulate', compact('order', 'bank_name', 'bank_account', 'bank_holder'));
})->name('payment.simulate');

// Language Switch Route
Route::get('/lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('lang.change');
