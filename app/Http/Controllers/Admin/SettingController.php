<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'maintenance'       => Setting::get('maintenance', '0'),
            'minimum_version'   => Setting::get('minimum_version', '1.0.0'),
            'notice'            => Setting::get('notice', ''),
            'trial_days'        => Setting::get('trial_days', '3'),
            'heartbeat_interval'=> Setting::get('heartbeat_interval', '300'),

            // PayOS Credentials
            'payos_client_id'   => Setting::get('payos_client_id', ''),
            'payos_api_key'     => Setting::get('payos_api_key', ''),
            'payos_checksum_key'=> Setting::get('payos_checksum_key', ''),

            // SePay Credentials
            'sepay_merchant_id' => Setting::get('sepay_merchant_id', ''),
            'sepay_api_key'     => Setting::get('sepay_api_key', ''),
            'sepay_env'         => Setting::get('sepay_env', 'sandbox'),
            'payment_gateway'   => Setting::get('payment_gateway', 'vietqr_only'),

            // Pricing Packages – 4 fully configurable slots
            'pkg_1_label'          => Setting::get('pkg_1_label', 'Gói tháng'),
            'pkg_1_days'           => Setting::get('pkg_1_days', '30'),
            'pkg_1_price'          => Setting::get('pkg_1_price', '150000'),
            'pkg_1_price_original' => Setting::get('pkg_1_price_original', ''),

            'pkg_2_label'          => Setting::get('pkg_2_label', 'Gói quý'),
            'pkg_2_days'           => Setting::get('pkg_2_days', '90'),
            'pkg_2_price'          => Setting::get('pkg_2_price', '400000'),
            'pkg_2_price_original' => Setting::get('pkg_2_price_original', ''),

            'pkg_3_label'          => Setting::get('pkg_3_label', 'Gói năm'),
            'pkg_3_days'           => Setting::get('pkg_3_days', '365'),
            'pkg_3_price'          => Setting::get('pkg_3_price', '1500000'),
            'pkg_3_price_original' => Setting::get('pkg_3_price_original', ''),

            'pkg_4_label'          => Setting::get('pkg_4_label', 'Vĩnh viễn'),
            'pkg_4_days'           => Setting::get('pkg_4_days', '0'),
            'pkg_4_price'          => Setting::get('pkg_4_price', '3000000'),
            'pkg_4_price_original' => Setting::get('pkg_4_price_original', ''),

            // Telegram Alerts
            'telegram_bot_token' => Setting::get('telegram_bot_token', ''),
            'telegram_chat_id'   => Setting::get('telegram_chat_id', ''),

            // Bank details for VietQR
            'bank_name'    => Setting::get('bank_name', ''),
            'bank_bin'     => Setting::get('bank_bin', ''),
            'bank_account' => Setting::get('bank_account', ''),
            'bank_holder'  => Setting::get('bank_holder', ''),

            // Contact & Support
            'contact_phone'    => Setting::get('contact_phone', ''),
            'contact_zalo'     => Setting::get('contact_zalo', ''),
            'contact_facebook' => Setting::get('contact_facebook', ''),
            'contact_email'    => Setting::get('contact_email', ''),
            'contact_website'  => Setting::get('contact_website', ''),
            'contact_note'     => Setting::get('contact_note', ''),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'maintenance'        => 'required|in:0,1',
            'minimum_version'    => 'required|string',
            'notice'             => 'nullable|string',
            'trial_days'         => 'required|integer|min:1',
            'heartbeat_interval' => 'required|integer|min:30',

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
        ]);

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

        return back()->with('success', "Settings updated successfully!");
    }
}
