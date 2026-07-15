<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $keys = [
            'maintenance'       => ['global' => true, 'default' => '0'],
            'minimum_version'   => ['global' => true, 'default' => '1.0.0'],
            'notice'            => ['global' => true, 'default' => ''],
            'trial_days'        => ['global' => true, 'default' => '3'],
            'heartbeat_interval'=> ['global' => true, 'default' => '300'],

            // PayOS Credentials
            'payos_client_id'   => ['global' => false, 'default' => ''],
            'payos_api_key'     => ['global' => false, 'default' => ''],
            'payos_checksum_key'=> ['global' => false, 'default' => ''],

            // SePay Credentials
            'sepay_merchant_id' => ['global' => false, 'default' => ''],
            'sepay_api_key'     => ['global' => false, 'default' => ''],
            'sepay_env'         => ['global' => false, 'default' => 'sandbox'],
            'payment_gateway'   => ['global' => false, 'default' => 'vietqr_only'],

            // Pricing Packages – 4 fully configurable slots
            'pkg_1_label'          => ['global' => false, 'default' => 'Gói tháng'],
            'pkg_1_days'           => ['global' => false, 'default' => '30'],
            'pkg_1_price'          => ['global' => false, 'default' => '150000'],
            'pkg_1_price_original' => ['global' => false, 'default' => ''],

            'pkg_2_label'          => ['global' => false, 'default' => 'Gói quý'],
            'pkg_2_days'           => ['global' => false, 'default' => '90'],
            'pkg_2_price'          => ['global' => false, 'default' => '400000'],
            'pkg_2_price_original' => ['global' => false, 'default' => ''],

            'pkg_3_label'          => ['global' => false, 'default' => 'Gói năm'],
            'pkg_3_days'           => ['global' => false, 'default' => '365'],
            'pkg_3_price'          => ['global' => false, 'default' => '1500000'],
            'pkg_3_price_original' => ['global' => false, 'default' => ''],

            'pkg_4_label'          => ['global' => false, 'default' => 'Vĩnh viễn'],
            'pkg_4_days'           => ['global' => false, 'default' => '0'],
            'pkg_4_price'          => ['global' => false, 'default' => '3000000'],
            'pkg_4_price_original' => ['global' => false, 'default' => ''],

            // Telegram Alerts
            'telegram_bot_token' => ['global' => false, 'default' => ''],
            'telegram_chat_id'   => ['global' => false, 'default' => ''],

            // Bank details for VietQR
            'bank_name'    => ['global' => false, 'default' => ''],
            'bank_bin'     => ['global' => false, 'default' => ''],
            'bank_account' => ['global' => false, 'default' => ''],
            'bank_holder'  => ['global' => false, 'default' => ''],

            // Contact & Support
            'contact_phone'    => ['global' => false, 'default' => ''],
            'contact_zalo'     => ['global' => false, 'default' => ''],
            'contact_facebook' => ['global' => false, 'default' => ''],
            'contact_email'    => ['global' => false, 'default' => ''],
            'contact_website'  => ['global' => false, 'default' => ''],
            'contact_note'     => ['global' => false, 'default' => ''],
        ];

        $settings = [];
        foreach ($keys as $key => $meta) {
            if ($meta['global']) {
                $settings[$key] = Setting::get($key, $meta['default']);
            } else {
                if ($isSuper) {
                    $settings[$key] = Setting::get($key, $meta['default']);
                } else {
                    $settings[$key] = $user->getSetting($key, $meta['default']);
                }
            }
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();

        $rules = [
            'payos_client_id'   => 'nullable|string',
            'payos_api_key'     => 'nullable|string',
            'payos_checksum_key'=> 'nullable|string',

            'sepay_merchant_id' => 'nullable|string',
            'sepay_api_key'     => 'nullable|string',
            'sepay_env'         => 'nullable|in:sandbox,production',
            'payment_gateway'   => 'required|in:payos,sepay,vietqr_only',

            'pkg_1_label'          => 'required|string|max:50',
            'pkg_1_days'           => 'required|integer|min:0',
            'pkg_1_price'          => 'required|integer|min:0',
            'pkg_1_price_original' => 'nullable|integer|min:0',

            'pkg_2_label'          => 'required|string|max:50',
            'pkg_2_days'           => 'required|integer|min:0',
            'pkg_2_price'          => 'required|integer|min:0',
            'pkg_2_price_original' => 'nullable|integer|min:0',

            'pkg_3_label'          => 'required|string|max:50',
            'pkg_3_days'           => 'required|integer|min:0',
            'pkg_3_price'          => 'required|integer|min:0',
            'pkg_3_price_original' => 'nullable|integer|min:0',

            'pkg_4_label'          => 'required|string|max:50',
            'pkg_4_days'           => 'required|integer|min:0',
            'pkg_4_price'          => 'required|integer|min:0',
            'pkg_4_price_original' => 'nullable|integer|min:0',

            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id'   => 'nullable|string',

            'bank_name'    => 'nullable|string',
            'bank_bin'     => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_holder'  => 'nullable|string',

            'contact_phone'    => 'nullable|string',
            'contact_zalo'     => 'nullable|string',
            'contact_facebook' => 'nullable|string',
            'contact_email'    => 'nullable|string',
            'contact_website'  => 'nullable|string',
            'contact_note'     => 'nullable|string',
        ];

        if ($isSuper) {
            $rules = array_merge($rules, [
                'maintenance'        => 'required|in:0,1',
                'minimum_version'    => 'required|string',
                'notice'             => 'nullable|string',
                'trial_days'         => 'required|integer|min:1',
                'heartbeat_interval' => 'required|integer|min:30',
            ]);
        }

        $request->validate($rules);

        if ($isSuper) {
            Setting::set('maintenance',        $request->maintenance);
            Setting::set('minimum_version',    $request->minimum_version);
            Setting::set('notice',             $request->notice ?? '');
            Setting::set('trial_days',         $request->trial_days);
            Setting::set('heartbeat_interval', $request->heartbeat_interval);

            Setting::set('payos_client_id',    $request->payos_client_id ?? '');
            Setting::set('payos_api_key',      $request->payos_api_key ?? '');
            Setting::set('payos_checksum_key', $request->payos_checksum_key ?? '');

            Setting::set('sepay_merchant_id',  $request->sepay_merchant_id ?? '');
            Setting::set('sepay_api_key',      $request->sepay_api_key ?? '');
            Setting::set('sepay_env',          $request->sepay_env ?? 'sandbox');
            Setting::set('payment_gateway',    $request->payment_gateway ?? 'vietqr_only');

            foreach ([1, 2, 3, 4] as $n) {
                Setting::set("pkg_{$n}_label",          $request->input("pkg_{$n}_label"));
                Setting::set("pkg_{$n}_days",           $request->input("pkg_{$n}_days"));
                Setting::set("pkg_{$n}_price",          $request->input("pkg_{$n}_price"));
                Setting::set("pkg_{$n}_price_original", $request->input("pkg_{$n}_price_original") ?? '');
            }

            Setting::set('telegram_bot_token', $request->telegram_bot_token ?? '');
            Setting::set('telegram_chat_id',   $request->telegram_chat_id ?? '');

            Setting::set('bank_name',    $request->bank_name ?? '');
            Setting::set('bank_bin',     $request->bank_bin ?? '');
            Setting::set('bank_account', $request->bank_account ?? '');
            Setting::set('bank_holder',  $request->bank_holder ?? '');

            Setting::set('contact_phone',    $request->contact_phone ?? '');
            Setting::set('contact_zalo',     $request->contact_zalo ?? '');
            Setting::set('contact_facebook', $request->contact_facebook ?? '');
            Setting::set('contact_email',    $request->contact_email ?? '');
            Setting::set('contact_website',  $request->contact_website ?? '');
            Setting::set('contact_note',     $request->contact_note ?? '');
        } else {
            $settings = $user->settings ?? [];

            $keysToSave = [
                'payos_client_id', 'payos_api_key', 'payos_checksum_key',
                'sepay_merchant_id', 'sepay_api_key', 'sepay_env', 'payment_gateway',
                'telegram_bot_token', 'telegram_chat_id',
                'bank_name', 'bank_bin', 'bank_account', 'bank_holder',
                'contact_phone', 'contact_zalo', 'contact_facebook', 'contact_email', 'contact_website', 'contact_note'
            ];

            foreach ([1, 2, 3, 4] as $n) {
                $keysToSave[] = "pkg_{$n}_label";
                $keysToSave[] = "pkg_{$n}_days";
                $keysToSave[] = "pkg_{$n}_price";
                $keysToSave[] = "pkg_{$n}_price_original";
            }

            foreach ($keysToSave as $k) {
                $settings[$k] = $request->input($k) ?? '';
            }

            $user->settings = $settings;
            $user->save();
        }

        return back()->with('success', "Settings updated successfully!");
    }

    public function checkSepay(Request $request)
    {
        $apiKey = $request->input('sepay_api_key');
        
        if (empty($apiKey)) {
            $user = auth()->user();
            if ($user->isSuperAdmin()) {
                $apiKey = Setting::get('sepay_api_key', '');
            } else {
                $apiKey = $user->getSetting('sepay_api_key', '');
            }
        }

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'API Key SePay không được để trống.'], 400);
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://qr.sepay.vn/api/merchant/profile');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Accept: application/json',
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                // SePay API trả về format data trực tiếp hoặc có bọc status
                $status = $data['status'] ?? 200;
                if ($status == 200 || $status == 'success') {
                    $merchantName = $data['data']['name'] ?? ($data['name'] ?? 'N/A');
                    return response()->json([
                        'success' => true, 
                        'message' => 'Kết nối thành công! Merchant: ' . $merchantName
                    ]);
                }
                return response()->json([
                    'success' => false, 
                    'message' => 'Lỗi phản hồi: ' . ($data['message'] ?? 'API Key không hợp lệ.')
                ], 400);
            }

            $msg = 'Không thể kết nối đến SePay.';
            if ($httpCode === 401) {
                $msg = 'API Key SePay không hợp lệ (Unauthorized).';
            } elseif ($httpCode === 403) {
                $msg = 'Bị từ chối truy cập (Forbidden).';
            } else {
                $msg .= ' Mã phản hồi HTTP: ' . $httpCode;
            }

            return response()->json([
                'success' => false, 
                'message' => $msg
            ], 400);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi kết nối: ' . $e->getMessage()], 500);
        }
    }
}
