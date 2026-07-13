<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin'),
                'role' => 'super_admin',
            ]
        );

        // Create Default Test User
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'is_active' => true,
                'max_licenses' => 20,
                'permissions' => [
                    'can_create_license' => true,
                    'can_manage_devices' => true,
                    'can_use_sepay' => true,
                ],
                'settings' => [
                    'payment_gateway' => 'vietqr_only',
                    'bank_name' => 'MBBank',
                    'bank_bin' => '970422',
                    'bank_account' => '0968686868',
                    'bank_holder' => 'NGUYEN VAN A',
                    'pkg_1_label' => 'Gói 30 ngày',
                    'pkg_1_days' => '30',
                    'pkg_1_price' => '150000',
                    'pkg_2_label' => 'Gói 90 ngày',
                    'pkg_2_days' => '90',
                    'pkg_2_price' => '400000',
                    'pkg_3_label' => 'Gói 365 ngày',
                    'pkg_3_days' => '365',
                    'pkg_3_price' => '1200000',
                    'pkg_4_label' => 'Gói Vĩnh Viễn',
                    'pkg_4_days' => '0',
                    'pkg_4_price' => '3000000',
                ]
            ]
        );

        // Create Default Settings
        $defaultSettings = [
            'maintenance' => '0',
            'minimum_version' => '1.0.0',
            'notice' => 'System is working normally. Thank you for using our app!',
            'trial_days' => '3',
            'heartbeat_interval' => '300',
            'sepay_merchant_id' => 'SP-LIVE-NDB76738',
            'sepay_api_key' => 'spsk_live_MEfSBdFcN6zq63X5uspojuVnsWLHHVz2',
            'sepay_env' => 'production',
            'payment_gateway' => 'sepay',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
