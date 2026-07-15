<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Enforce super_admin access
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized access. Only Super Admin can manage users.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,user',
            'max_licenses' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $permissions = [
            'can_create_license' => $request->has('perm_create_license'),
            'can_manage_devices' => $request->has('perm_manage_devices'),
            'can_use_sepay' => $request->has('perm_use_sepay'),
        ];

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'max_licenses' => (int) $request->max_licenses,
            'is_active' => (bool) $request->is_active,
            'permissions' => $permissions,
        ]);

        return back()->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,user',
            'max_licenses' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }

        $request->validate($rules);

        $permissions = [
            'can_create_license' => $request->has('perm_create_license'),
            'can_manage_devices' => $request->has('perm_manage_devices'),
            'can_use_sepay' => $request->has('perm_use_sepay'),
        ];

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'max_licenses' => (int) $request->max_licenses,
            'is_active' => (bool) $request->is_active,
            'permissions' => $permissions,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', 'User updated successfully!');
    }

    public function details(User $user)
    {
        $licenses = $user->licenses()->withCount('devices')->orderBy('created_at', 'desc')->get();
        
        $totalLicenses = $licenses->count();
        $totalDevices = 0;
        foreach ($licenses as $lic) {
            $totalDevices += $lic->devices_count;
        }

        $formattedLicenses = $licenses->map(function ($lic) {
            return [
                'license_key' => $lic->license_key,
                'status' => $lic->status,
                'expire_at' => $lic->expire_at ? $lic->expire_at->format('Y-m-d H:i') : 'Vĩnh viễn',
                'product_name' => $lic->product_name ?: 'Dùng chung',
                'devices_count' => $lic->devices_count,
                'max_devices' => $lic->max_devices,
            ];
        });

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'max_licenses' => $user->max_licenses,
                'created_at' => $user->created_at->format('Y-m-d H:i'),
            ],
            'stats' => [
                'total_licenses' => $totalLicenses,
                'total_devices' => $totalDevices,
            ],
            'licenses' => $formattedLicenses,
        ]);
    }

    public function destroy(User $user)
    {
        // Don't allow self deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }
}
