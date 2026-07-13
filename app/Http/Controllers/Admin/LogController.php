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
}
