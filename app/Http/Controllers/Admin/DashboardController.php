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
        // Stats
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
