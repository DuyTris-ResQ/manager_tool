<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\License;
use App\Models\Log as ClientLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SoftwareVersion;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * POST /api/auth/device
     * Register or authenticate a device, auto-generating a trial if no license exists.
     */
    public function authDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'computer_name' => 'required|string',
            'cpu' => 'nullable|string',
            'gpu' => 'nullable|string',
            'os' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $ip = $request->ip();

        // 1. Find or create device
        $device = Device::where('device_id', $request->device_id)->first();

        if (!$device) {
            $device = Device::create([
                'device_id' => $request->device_id,
                'computer_name' => $request->computer_name,
                'cpu' => $request->cpu,
                'gpu' => $request->gpu,
                'os' => $request->os,
                'ip' => $ip,
                'app_version' => $request->app_version,
                'first_login' => Carbon::now(),
                'last_online' => Carbon::now(),
                'is_online' => true,
            ]);
        } else {
            $device->update([
                'computer_name' => $request->computer_name,
                'cpu' => $request->cpu ?: $device->cpu,
                'gpu' => $request->gpu ?: $device->gpu,
                'os' => $request->os ?: $device->os,
                'ip' => $ip,
                'app_version' => $request->app_version ?: $device->app_version,
                'last_online' => Carbon::now(),
                'is_online' => true,
            ]);
        }

        // 2. Check if device is linked to a license
        if (!$device->license_id) {
            // Auto generate trial license
            $trialDays = (int) Setting::get('trial_days', 3);
            $licenseKey = 'TRIAL-' . strtoupper(Str::random(16));

            $license = License::create([
                'license_key' => $licenseKey,
                'status' => 'trial',
                'trial_start' => Carbon::now(),
                'expire_at' => Carbon::now()->addDays($trialDays),
                'max_devices' => 1,
            ]);

            $device->update(['license_id' => $license->id]);

            // Log the activation event
            ClientLog::create([
                'device_id' => $device->device_id,
                'type' => 'activate',
                'message' => "Auto-created trial license: {$licenseKey}",
            ]);
        } else {
            $license = $device->license;
        }

        // 3. Return license info
        return response()->json([
            'success' => true,
            'device' => [
                'device_id' => $device->device_id,
                'computer_name' => $device->computer_name,
            ],
            'license' => [
                'license_key' => $license->license_key,
                'status' => $license->status,
                'expire_at' => $license->expire_at ? $license->expire_at->toIso8601String() : null,
                'is_valid' => $license->isValid(),
            ],
        ]);
    }

    /**
     * POST /api/license/check
     * Check if a license is valid for a given device.
     */
    public function checkLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'license_key' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $device = Device::where('device_id', $request->device_id)->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not registered.'], 404);
        }

        // If license key is provided, check/bind it
        if ($request->license_key) {
            $license = License::where('license_key', $request->license_key)->first();
            if (!$license) {
                return response()->json(['success' => false, 'message' => 'License not found.'], 404);
            }

            if (!$license->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => "License is {$license->status}.",
                    'status' => $license->status
                ], 403);
            }

            // Bind if not already bound
            if ($device->license_id !== $license->id) {
                if ($license->devices()->count() >= $license->max_devices) {
                    return response()->json(['success' => false, 'message' => 'Max devices limit reached for this license.'], 400);
                }
                $device->update(['license_id' => $license->id]);
            }
        } else {
            $license = $device->license;
            if (!$license) {
                return response()->json(['success' => false, 'message' => 'No license associated with this device.'], 404);
            }
        }

        $device->update(['last_online' => Carbon::now(), 'is_online' => true]);

        return response()->json([
            'success' => true,
            'license' => [
                'license_key' => $license->license_key,
                'status' => $license->status,
                'expire_at' => $license->expire_at ? $license->expire_at->toIso8601String() : null,
                'is_valid' => $license->isValid(),
            ],
        ]);
    }

    /**
     * POST /api/license/activate
     * Bind a device to a specific license key.
     */
    public function activateLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'license_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $device = Device::where('device_id', $request->device_id)->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not registered. Please call auth first.'], 404);
        }

        $license = License::where('license_key', $request->license_key)->first();
        if (!$license) {
            return response()->json(['success' => false, 'message' => 'License key not found.'], 404);
        }

        if (!$license->isValid()) {
            return response()->json([
                'success' => false,
                'message' => "License is {$license->status}.",
                'status' => $license->status
            ], 403);
        }

        // If already linked, return success
        if ($device->license_id === $license->id) {
            return response()->json([
                'success' => true,
                'message' => 'License already active on this device.',
                'license' => [
                    'license_key' => $license->license_key,
                    'status' => $license->status,
                    'expire_at' => $license->expire_at ? $license->expire_at->toIso8601String() : null,
                ],
            ]);
        }

        // Verify limit
        if ($license->devices()->count() >= $license->max_devices) {
            return response()->json([
                'success' => false,
                'message' => "Max device limit ({$license->max_devices}) reached for this license. Please release other devices first."
            ], 400);
        }

        // Link device
        $device->update(['license_id' => $license->id, 'last_online' => Carbon::now(), 'is_online' => true]);

        ClientLog::create([
            'device_id' => $device->device_id,
            'type' => 'activate',
            'message' => "Linked to license: {$license->license_key}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'License activated successfully on this device.',
            'license' => [
                'license_key' => $license->license_key,
                'status' => $license->status,
                'expire_at' => $license->expire_at ? $license->expire_at->toIso8601String() : null,
            ],
        ]);
    }

    /**
     * POST /api/heartbeat
     * Receive heartbeat from device, updating online status.
     */
    public function heartbeat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $device = Device::where('device_id', $request->device_id)->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $device->update([
            'last_online' => Carbon::now(),
            'is_online' => true,
        ]);

        return response()->json([
            'success' => true,
            'heartbeat_interval' => (int) Setting::get('heartbeat_interval', 300),
        ]);
    }

    /**
     * POST /api/version
     * Check software version status.
     */
    public function checkVersion(Request $request)
    {
        $currentVersion = $request->input('version');

        $latest = SoftwareVersion::orderBy('created_at', 'desc')->first();

        if (!$latest) {
            // Default response if no versions created yet
            return response()->json([
                'success' => true,
                'update_available' => false,
                'latest_version' => '1.0.0',
                'force_update' => false,
                'download_url' => '',
            ]);
        }

        $updateAvailable = false;
        if ($currentVersion) {
            $updateAvailable = version_compare($currentVersion, $latest->version, '<');
        }

        $minVersion = Setting::get('minimum_version', '1.0.0');
        $forceUpdate = $latest->force_update;
        if ($currentVersion && version_compare($currentVersion, $minVersion, '<')) {
            $forceUpdate = true;
        }

        return response()->json([
            'success' => true,
            'update_available' => $updateAvailable,
            'latest_version' => $latest->version,
            'force_update' => $forceUpdate,
            'download_url' => $latest->download_url,
            'release_note' => $latest->release_note,
        ]);
    }

    /**
     * POST /api/payment/create
     * Create a payment link for a license extension.
     */
    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'amount' => 'required|numeric|min:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $license = License::where('license_key', $request->license_key)->first();
        if (!$license) {
            return response()->json(['success' => false, 'message' => 'License not found.'], 404);
        }

        $orderCode = (string) rand(100000, 999999);
        $amount = (float) $request->amount;

        // Create Order and Payment record in DB
        $order = Order::create([
            'license_id' => $license->id,
            'order_code' => $orderCode,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $gateway = Setting::get('payment_gateway', 'vietqr_only');

        $payment = Payment::create([
            'license_id' => $license->id,
            'order_code' => $orderCode,
            'provider' => $gateway,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Generate checkout URL.
        // We will generate a mock PayOS checkout URL for ease of local validation, which directs
        // to our local test success webhook simulator!
        $checkoutUrl = url("/payment/pay/{$orderCode}");

        return response()->json([
            'success' => true,
            'order_code' => $orderCode,
            'amount' => $amount,
            'checkout_url' => $checkoutUrl,
        ]);
    }

    /**
     * POST /api/payment/webhook
     * Receive payment notifications from PayOS/SePay and activate license.
     */
    public function webhook(Request $request)
    {
        // 1. Log webhook call
        ClientLog::create([
            'type' => 'payment',
            'message' => 'Payment Webhook Called',
            'details' => $request->all(),
        ]);

        // 2. Parse payload.
        // SePay uses code/transferAmount/referenceCode/transferType.
        // PayOS uses data field (orderCode, amount, status).
        $data = $request->input('data');
        if (!$data) {
            $data = $request->all(); // Fallback to raw payload
        }

        // SePay fields fallback
        $orderCode = $data['code'] ?? $data['orderCode'] ?? $data['order_code'] ?? null;
        $amount = $data['transferAmount'] ?? $data['amount'] ?? 0;
        
        // SePay is only triggered for incoming success transfers.
        // If transferAmount or transferType is present, it's SePay, status is success.
        $isSePay = isset($data['transferAmount']) || isset($data['transferType']);
        $status = $data['status'] ?? 'success';

        // Check if SePay sent an outgoing transaction (e.g. transferType === 'out')
        if (isset($data['transferType']) && $data['transferType'] !== 'in') {
            return response()->json(['success' => false, 'message' => 'Ignore outgoing transaction.'], 200);
        }

        if (!$orderCode) {
            return response()->json(['success' => false, 'message' => 'Missing order code.'], 400);
        }

        $payment = Payment::where('order_code', $orderCode)->first();
        $order = Order::where('order_code', $orderCode)->first();

        if (!$payment || !$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($payment->status === 'completed') {
            return response()->json(['success' => true, 'message' => 'Payment already processed.']);
        }

        $isSuccess = $isSePay || in_array(strtolower($status), ['success', 'completed', 'paid']);

        if ($isSuccess) {
            // Determine days to extend based on matching package price
            $daysToExtend = 30; // fallback default
            foreach ([1, 2, 3, 4] as $n) {
                $pkgPrice = (int) Setting::get("pkg_{$n}_price", 0);
                $pkgDays = (int) Setting::get("pkg_{$n}_days", 0);
                if ($pkgPrice > 0 && abs($pkgPrice - (int)$amount) < 10) {
                    $daysToExtend = $pkgDays;
                    break;
                }
            }

            $payment->update([
                'status' => 'completed',
                'paid_at' => Carbon::now(),
                'transaction_code' => $data['referenceCode'] ?? $data['reference'] ?? $data['transaction_code'] ?? Str::random(10),
                'raw_data' => $request->all(),
            ]);

            $order->update(['status' => 'completed']);

            // 3. Extend/Activate License
            $license = $payment->license;

            if ($daysToExtend === 0) {
                $newExpire = null; // Lifetime license
            } else {
                $currentExpire = $license->expire_at;
                if (!$currentExpire || $currentExpire->isPast()) {
                    $newExpire = Carbon::now()->addDays($daysToExtend);
                } else {
                    $newExpire = $currentExpire->addDays($daysToExtend);
                }
            }

            $license->update([
                'status' => 'active',
                'expire_at' => $newExpire,
            ]);

            // Log event
            ClientLog::create([
                'device_id' => $license->devices()->first()?->device_id,
                'type' => 'payment',
                'message' => "License extended (" . ($daysToExtend === 0 ? "Lifetime" : "{$daysToExtend} days") . ") to " . ($newExpire ? $newExpire->toDateString() : "Lifetime") . " via payment: {$orderCode}",
            ]);

            return response()->json(['success' => true, 'message' => 'License activated/extended successfully.']);
        } else {
            $payment->update(['status' => 'failed', 'raw_data' => $request->all()]);
            $order->update(['status' => 'failed']);
            return response()->json(['success' => true, 'message' => 'Payment marked as failed.']);
        }
    }

    /**
     * POST /api/log
     * Upload crash or system logs from client.
     */
    public function uploadLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'message' => 'required|string',
            'device_id' => 'nullable|string',
            'details' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        ClientLog::create([
            'device_id' => $request->device_id,
            'type' => $request->type,
            'message' => $request->message,
            'details' => $request->details,
        ]);

        return response()->json(['success' => true, 'message' => 'Log uploaded successfully.']);
    }

    /**
     * GET /api/settings
     * Get public application settings.
     */
    public function getSettings()
    {
        $packages = [];
        foreach ([1, 2, 3, 4] as $n) {
            $origRaw = Setting::get("pkg_{$n}_price_original", '');
            $packages[] = [
                'key'            => "pkg_{$n}",
                'label'          => Setting::get("pkg_{$n}_label", "Gói {$n}"),
                'days'           => (int) Setting::get("pkg_{$n}_days", 30),
                'price'          => (int) Setting::get("pkg_{$n}_price", 0),
                'price_original' => $origRaw !== '' ? (int) $origRaw : null,
            ];
        }

        return response()->json([
            'success'            => true,
            'maintenance'        => (bool) Setting::get('maintenance', 0),
            'minimum_version'    => Setting::get('minimum_version', '1.0.0'),
            'notice'             => Setting::get('notice', 'System is active.'),
            'trial_days'         => (int) Setting::get('trial_days', 3),
            'heartbeat_interval' => (int) Setting::get('heartbeat_interval', 300),
            'packages'           => $packages,
            'payment_gateway'    => Setting::get('payment_gateway', 'vietqr_only'),
            'bank_name'          => Setting::get('bank_name', ''),
            'bank_bin'           => Setting::get('bank_bin', ''),
            'bank_account'       => Setting::get('bank_account', ''),
            'bank_holder'        => Setting::get('bank_holder', ''),
            'contact_phone'      => Setting::get('contact_phone', ''),
            'contact_zalo'       => Setting::get('contact_zalo', ''),
            'contact_facebook'   => Setting::get('contact_facebook', ''),
            'contact_email'      => Setting::get('contact_email', ''),
            'contact_website'    => Setting::get('contact_website', ''),
            'contact_note'       => Setting::get('contact_note', ''),
        ]);
    }
}
