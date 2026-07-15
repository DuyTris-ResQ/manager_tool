<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log as ClientLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ClientLog::query();

        if (!$user->isSuperAdmin()) {
            $query->whereHas('device.license', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('device_id', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }

    public function destroy(ClientLog $log)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            if (!$log->device_id) {
                abort(403, 'Unauthorized action.');
            }
            $device = \App\Models\Device::where('device_id', $log->device_id)->first();
            if (!$device || !$device->license_id || $device->license->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $log->delete();
        return back()->with('success', 'Xóa nhật ký thành công!');
    }

    public function clear()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action. Only Super Admin can clear logs.');
        }

        ClientLog::truncate();
        return back()->with('success', 'Đã xóa toàn bộ nhật ký hệ thống!');
    }
}
