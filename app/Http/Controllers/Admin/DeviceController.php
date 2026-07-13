<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Device::with('license');

        if (!$user->isSuperAdmin()) {
            $query->whereHas('license', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Search spec, name or device id
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('computer_name', 'like', "%{$request->search}%")
                  ->orWhere('device_id', 'like', "%{$request->search}%")
                  ->orWhere('cpu', 'like', "%{$request->search}%")
                  ->orWhere('gpu', 'like', "%{$request->search}%");
            });
        }

        // Online status filter
        if ($request->has('online')) {
            $query->where('is_online', $request->online);
        }

        $devices = $query->orderBy('last_online', 'desc')->paginate(10)->withQueryString();

        return view('admin.devices.index', compact('devices'));
    }

    protected function checkOwnership(Device $device)
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return;
        }

        if (!$user->hasPermission('can_manage_devices')) {
            abort(403, 'Tài khoản của bạn không được cấp quyền quản lý thiết bị.');
        }

        if (!$device->license_id || $device->license->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Remove Device: Unlinks the device from its license.
     */
    public function remove(Device $device)
    {
        $this->checkOwnership($device);
        $device->update(['license_id' => null]);
        return back()->with('success', "Device {$device->computer_name} unlinked from license successfully!");
    }

    /**
     * Block Device: Deletes the device record.
     */
    public function block(Device $device)
    {
        $this->checkOwnership($device);
        $device->delete();
        return back()->with('success', "Device {$device->computer_name} blocked/removed successfully!");
    }
}
