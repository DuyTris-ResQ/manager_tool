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
        $user = auth()->user();
        $query = License::withCount('devices');

        if (!$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        } else {
            $query->with('user');
        }

        // Smart Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_key', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('devices', function ($dq) use ($search) {
                      $dq->where('device_id', 'like', "%{$search}%")
                         ->orWhere('computer_name', 'like', "%{$search}%");
                  });
            });
        }

        // Product Name filter
        if ($request->product_name) {
            if ($request->product_name === 'default') {
                $query->where(function($q) {
                    $q->whereNull('product_name')->orWhere('product_name', '');
                });
            } else {
                $query->where('product_name', $request->product_name);
            }
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $licenses = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        // Get all unique product names for filter dropdown
        $allProductsQuery = License::select('product_name')->distinct();
        if (!$user->isSuperAdmin()) {
            $allProductsQuery->where('user_id', $user->id);
        }
        $products = $allProductsQuery->whereNotNull('product_name')->where('product_name', '!=', '')->pluck('product_name')->toArray();

        $users = [];
        if ($user->isSuperAdmin()) {
            $users = \App\Models\User::where('role', '!=', 'super_admin')->get();
        }

        return view('admin.licenses.index', compact('licenses', 'users', 'products'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $rules = [
            'max_devices' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:trial,active,disabled,banned',
            'product_name' => 'nullable|string|max:255',
        ];
        
        if ($user->isSuperAdmin()) {
            $rules['user_id'] = 'nullable|exists:users,id';
        }
        
        $request->validate($rules);

        $userId = $user->isSuperAdmin() ? $request->user_id : $user->id;
        $targetUser = $userId ? User::find($userId) : null;

        // Enforce permissions and max license limits
        if ($targetUser && !$targetUser->isSuperAdmin()) {
            if (!$targetUser->hasPermission('can_create_license')) {
                return back()->with('error', 'Tài khoản người sở hữu không được cấp quyền tạo Giấy phép.');
            }

            if ($targetUser->max_licenses > 0 && $targetUser->licenses()->count() >= $targetUser->max_licenses) {
                return back()->with('error', 'Tài khoản người sở hữu đã đạt giới hạn số lượng Giấy phép tối đa (' . $targetUser->max_licenses . ').');
            }
        }

        $licenseKey = 'KEY-' . strtoupper(Str::random(16));

        License::create([
            'license_key' => $licenseKey,
            'status' => $request->status,
            'expire_at' => Carbon::now()->addDays((int) $request->duration_days),
            'max_devices' => $request->max_devices,
            'trial_start' => $request->status === 'trial' ? Carbon::now() : null,
            'user_id' => $userId,
            'product_name' => $request->product_name ?: null,
        ]);

        return back()->with('success', "License {$licenseKey} created successfully!");
    }

    protected function checkOwnership(License $license)
    {
        if (!auth()->user()->isSuperAdmin() && $license->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function update(Request $request, License $license)
    {
        $this->checkOwnership($license);
        
        $request->validate([
            'status' => 'required|in:trial,active,expired,disabled,banned',
        ]);

        $license->update(['status' => $request->status]);

        return back()->with('success', "License status updated successfully!");
    }

    public function extend(Request $request, License $license)
    {
        $this->checkOwnership($license);
        
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
            'status' => ($license->status === 'expired' || $license->status === 'trial') ? 'active' : $license->status
        ]);

        return back()->with('success', "License extended by {$request->days} days! New expiry: " . $newExpire->toDateString());
    }

    public function changeMaxDevices(Request $request, License $license)
    {
        $this->checkOwnership($license);
        
        $request->validate([
            'max_devices' => 'required|integer|min:1',
        ]);

        $license->update(['max_devices' => $request->max_devices]);

        return back()->with('success', "Max devices updated to {$request->max_devices}!");
    }

    public function quickUpdate(Request $request, License $license)
    {
        $this->checkOwnership($license);
        
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
        $this->checkOwnership($license);
        
        $license->delete();
        return back()->with('success', "License deleted successfully!");
    }

    public function bulkAction(Request $request)
    {
        $user = auth()->user();
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một bản quyền để thực hiện.');
        }

        $query = License::whereIn('id', $ids);
        if (!$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        $resolvedLicenses = $query->get();

        if ($action === 'delete') {
            foreach ($resolvedLicenses as $lic) {
                $lic->devices()->update(['license_id' => null]);
                $lic->delete();
            }
            return back()->with('success', 'Đã xóa hàng loạt ' . count($resolvedLicenses) . ' bản quyền thành công.');
        }

        if ($action === 'unlink') {
            foreach ($resolvedLicenses as $lic) {
                $lic->devices()->update(['license_id' => null]);
            }
            return back()->with('success', 'Đã gỡ thiết bị hàng loạt cho ' . count($resolvedLicenses) . ' bản quyền.');
        }

        if ($action === 'export_txt') {
            $content = "";
            foreach ($resolvedLicenses as $lic) {
                $content .= $lic->license_key . "\r\n";
            }
            return response($content, 200, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="licenses_' . date('Ymd_His') . '.txt"',
            ]);
        }

        if ($action === 'export_csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="licenses_' . date('Ymd_His') . '.csv"',
            ];
            $callback = function() use ($resolvedLicenses) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM to prevent excel issues
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['License Key', 'App/Product', 'Status', 'Devices Count', 'Max Devices', 'Owner', 'Expires At', 'Created At']);
                foreach ($resolvedLicenses as $lic) {
                    fputcsv($file, [
                        $lic->license_key,
                        $lic->product_name ?: 'All',
                        $lic->status,
                        $lic->devices()->count(),
                        $lic->max_devices,
                        $lic->user ? $lic->user->name : 'System',
                        $lic->expire_at ? $lic->expire_at->toDateTimeString() : 'Lifetime',
                        $lic->created_at->toDateTimeString()
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'Hành động không hợp lệ.');
    }
}
