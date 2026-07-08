<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $query = License::withCount('devices');

        // Search
        if ($request->search) {
            $query->where('license_key', 'like', "%{$request->search}%");
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $licenses = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.licenses.index', compact('licenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'max_devices' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:trial,active,disabled,banned',
        ]);

        $licenseKey = 'KEY-' . strtoupper(Str::random(16));

        License::create([
            'license_key' => $licenseKey,
            'status' => $request->status,
            'expire_at' => Carbon::now()->addDays((int) $request->duration_days),
            'max_devices' => $request->max_devices,
            'trial_start' => $request->status === 'trial' ? Carbon::now() : null,
        ]);

        return back()->with('success', "License {$licenseKey} created successfully!");
    }

    public function update(Request $request, License $license)
    {
        $request->validate([
            'status' => 'required|in:trial,active,expired,disabled,banned',
        ]);

        $license->update(['status' => $request->status]);

        return back()->with('success', "License status updated successfully!");
    }

    public function extend(Request $request, License $license)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $currentExpire = $license->expire_at;
        if (!$currentExpire || $currentExpire->isPast()) {
            $newExpire = Carbon::now()->addDays((int) $request->days);
        } else {
            $newExpire = $currentExpire->addDays((int) $request->days);
        }

        $license->update([
            'expire_at' => $newExpire,
            // If it was expired, we set it back to active or trial depending on original status
            'status' => ($license->status === 'expired' || $license->status === 'trial') ? 'active' : $license->status
        ]);

        return back()->with('success', "License extended by {$request->days} days! New expiry: " . $newExpire->toDateString());
    }

    public function changeMaxDevices(Request $request, License $license)
    {
        $request->validate([
            'max_devices' => 'required|integer|min:1',
        ]);

        $license->update(['max_devices' => $request->max_devices]);

        return back()->with('success', "Max devices updated to {$request->max_devices}!");
    }

    public function quickUpdate(Request $request, License $license)
    {
        $request->validate([
            'status' => 'required|in:trial,active,expired,disabled,banned',
            'max_devices' => 'required|integer|min:1',
            'extend_days' => 'nullable|integer|min:0',
        ]);

        $updateData = [
            'status' => $request->status,
            'max_devices' => $request->max_devices,
        ];

        if ($request->filled('extend_days') && (int)$request->extend_days > 0) {
            $currentExpire = $license->expire_at;
            if (!$currentExpire || $currentExpire->isPast()) {
                $updateData['expire_at'] = Carbon::now()->addDays((int) $request->extend_days);
            } else {
                $updateData['expire_at'] = $currentExpire->addDays((int) $request->extend_days);
            }
        }

        $license->update($updateData);

        return back()->with('success', "License updated successfully!");
    }

    public function destroy(License $license)
    {
        $license->delete();
        return back()->with('success', "License deleted successfully!");
    }
}
