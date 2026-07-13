<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\License;
use App\Models\Log as ClientLog;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();
        $userId = $user->id;

        // Stats
        if ($isSuper) {
            $totalRevenue = Payment::where('status', 'completed')->sum('amount');
            $totalLicenses = License::count();
            $activeLicenses = License::where('status', 'active')->count();
            $trialLicenses = License::where('status', 'trial')->count();
            $onlineDevices = Device::where('is_online', true)->count();
            $totalDevices = Device::count();

            // Recent items
            $recentPayments = Payment::orderBy('created_at', 'desc')->limit(5)->get();
            $recentLogs = ClientLog::orderBy('created_at', 'desc')->limit(5)->get();
            $recentDevices = Device::orderBy('last_online', 'desc')->limit(5)->get();
        } else {
            $totalRevenue = Payment::where('status', 'completed')
                ->whereHas('license', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->sum('amount');

            $totalLicenses = License::where('user_id', $userId)->count();
            $activeLicenses = License::where('user_id', $userId)->where('status', 'active')->count();
            $trialLicenses = License::where('user_id', $userId)->where('status', 'trial')->count();

            $onlineDevices = Device::where('is_online', true)
                ->whereHas('license', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->count();

            $totalDevices = Device::whereHas('license', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();

            // Recent items
            $recentPayments = Payment::whereHas('license', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orderBy('created_at', 'desc')->limit(5)->get();

            $recentLogs = ClientLog::whereHas('device.license', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orderBy('created_at', 'desc')->limit(5)->get();

            $recentDevices = Device::whereHas('license', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orderBy('last_online', 'desc')->limit(5)->get();
        }

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'totalLicenses',
            'activeLicenses',
            'trialLicenses',
            'onlineDevices',
            'totalDevices',
            'recentPayments',
            'recentLogs',
            'recentDevices'
        ));
    }
}
