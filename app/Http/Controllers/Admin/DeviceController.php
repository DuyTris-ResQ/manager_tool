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

        // Smart Search: spec, name, device id, IP, or license key
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('computer_name', 'like', "%{$search}%")
                  ->orWhere('device_id', 'like', "%{$search}%")
                  ->orWhere('cpu', 'like', "%{$search}%")
                  ->orWhere('gpu', 'like', "%{$search}%")
                  ->orWhere('ip', 'like', "%{$search}%")
                  ->orWhereHas('license', function ($lq) use ($search) {
                      $lq->where('license_key', 'like', "%{$search}%");
                  });
            });
        }

        // Product Name filter
        if ($request->product_name) {
            $prod = $request->product_name;
            if ($prod === 'default') {
                $query->where(function($q) {
                    $q->where(function($dq) {
                        $dq->whereNull('product_name')->orWhere('product_name', '');
                    })->where(function($dq2) {
                        $dq2->whereDoesntHave('license')
                            ->orWhereHas('license', function($lq) {
                                $lq->whereNull('product_name')->orWhere('product_name', '');
                            });
                    });
                });
            } else {
                $query->where(function($q) use ($prod) {
                    $q->where('product_name', $prod)
                      ->orWhereHas('license', function($lq) use ($prod) {
                          $lq->where('product_name', $prod);
                      });
                });
            }
        }

        // Online status filter
        if ($request->has('online') && $request->online !== null && $request->online !== '') {
            $query->where('is_online', $request->online);
        }

        $devices = $query->orderBy('last_online', 'desc')->paginate(10)->withQueryString();

        // Get all unique products from both devices and licenses
        $devProdsQuery = Device::select('product_name')->distinct();
        if (!$user->isSuperAdmin()) {
            $devProdsQuery->whereHas('license', function($lq) use ($user) {
                $lq->where('user_id', $user->id);
            });
        }
        $deviceProducts = $devProdsQuery->whereNotNull('product_name')->where('product_name', '!=', '')->pluck('product_name')->toArray();

        $licProdsQuery = \App\Models\License::select('product_name')->distinct();
        if (!$user->isSuperAdmin()) {
            $licProdsQuery->where('user_id', $user->id);
        }
        $licProducts = $licProdsQuery->whereNotNull('product_name')->where('product_name', '!=', '')->pluck('product_name')->toArray();

        $products = array_values(array_unique(array_merge($deviceProducts, $licProducts)));

        return view('admin.devices.index', compact('devices', 'products'));
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

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:unlink,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:devices,id',
        ]);

        $user = auth()->user();
        $action = $request->action;
        $ids = $request->ids;

        $devices = Device::whereIn('id', $ids)->get();

        $count = 0;
        foreach ($devices as $device) {
            try {
                $this->checkOwnership($device);
                if ($action === 'unlink') {
                    $device->update(['license_id' => null]);
                } elseif ($action === 'delete') {
                    $device->delete();
                }
                $count++;
            } catch (\Exception $e) {
                // Silent bypass on exception to perform on maximum possible devices
            }
        }

        $msg = $action === 'unlink' 
            ? "Đã gỡ liên kết thành công cho {$count} thiết bị." 
            : "Đã xóa (block) thành công {$count} thiết bị.";

        return back()->with('success', $msg);
    }
}
