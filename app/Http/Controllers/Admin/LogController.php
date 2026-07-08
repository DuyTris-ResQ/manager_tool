<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log as ClientLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientLog::query();

        if ($request->search) {
            $query->where('device_id', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
