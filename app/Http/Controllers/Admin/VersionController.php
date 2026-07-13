<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareVersion;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }
    public function index()
    {
        $versions = SoftwareVersion::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.versions.index', compact('versions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
            'download_url' => 'required|url',
            'force_update' => 'nullable|boolean',
            'release_note' => 'nullable|string',
        ]);

        SoftwareVersion::create([
            'version' => $request->version,
            'download_url' => $request->download_url,
            'force_update' => $request->has('force_update') ? (bool) $request->force_update : false,
            'release_note' => $request->release_note,
        ]);

        return back()->with('success', "Version {$request->version} added successfully!");
    }

    public function destroy(SoftwareVersion $version)
    {
        $version->delete();
        return back()->with('success', "Version deleted successfully!");
    }
}
