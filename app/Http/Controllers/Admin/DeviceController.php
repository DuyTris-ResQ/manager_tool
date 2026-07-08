<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('license');

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

    /**
     * Remove Device: Unlinks the device from its license.
     */
    public function remove(Device $device)
    {
        $device->update(['license_id' => null]);
        return back()->with('success', "Device {$device->computer_name} unlinked from license successfully!");
    }

    /**
     * Block Device: Deletes the device record.
     */
    public function block(Device $device)
    {
        $device->delete();
        return back()->with('success', "Device {$device->computer_name} blocked/removed successfully!");
    }
}
