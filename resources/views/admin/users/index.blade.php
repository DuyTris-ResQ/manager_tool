@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng - License Management Admin')
@section('page_title', 'Quản lý Người dùng')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 mb-6">
    <!-- Search Form -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc email..." class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm sm:w-64">
        <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-2xl border border-emerald-500 text-emerald-600 bg-white hover:bg-emerald-50 transition-colors">
            Tìm kiếm
        </button>
    </form>

    <!-- Create User Button -->
    <button onclick="openModal('create-user-modal')" class="px-6 py-2.5 text-sm font-bold rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 text-white hover:from-cyan-600 hover:to-emerald-600 shadow-md shadow-cyan-200/50 transition-all flex items-center justify-center space-x-1.5 self-start md:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Thêm Người dùng</span>
    </button>
</div>

<!-- Users Table Card -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold bg-gray-50/50">
                    <th class="p-4">Tên</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Vai trò</th>
                    <th class="p-4">Trạng thái</th>
                    <th class="p-4">Giới hạn Key</th>
                    <th class="p-4">Quyền hạn</th>
                    <th class="p-4">Ngày tạo</th>
                    <th class="p-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4 font-semibold text-gray-800">
                            {{ $user->name }}
                        </td>
                        <td class="p-4 font-mono text-gray-650">
                            {{ $user->email }}
                        </td>
                        <td class="p-4">
                            @if($user->role === 'super_admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">Super Admin</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">User thường</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Hoạt động</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">Tạm khóa</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-700 font-medium">
                            @if($user->isSuperAdmin())
                                <span class="text-gray-400 italic">N/A</span>
                            @else
                                {{ $user->max_licenses > 0 ? $user->max_licenses . ' Keys' : 'Không giới hạn' }}
                            @endif
                        </td>
                        <td class="p-4">
                            @if($user->isSuperAdmin())
                                <span class="text-xs text-gray-400 italic">Full permissions</span>
                            @else
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="px-2 py-0.5 rounded border {{ $user->hasPermission('can_create_license') ? 'border-emerald-200 bg-emerald-50 text-emerald-700 font-bold' : 'border-gray-200 bg-gray-50 text-gray-400 line-through' }}" title="Quyền tạo Key">🔑 Key</span>
                                    <span class="px-2 py-0.5 rounded border {{ $user->hasPermission('can_manage_devices') ? 'border-emerald-200 bg-emerald-50 text-emerald-700 font-bold' : 'border-gray-200 bg-gray-50 text-gray-400 line-through' }}" title="Quyền quản lý thiết bị">🖥️ Máy</span>
                                    <span class="px-2 py-0.5 rounded border {{ $user->hasPermission('can_use_sepay') ? 'border-emerald-200 bg-emerald-50 text-emerald-700 font-bold' : 'border-gray-200 bg-gray-50 text-gray-400 line-through' }}" title="Quyền dùng cổng Sepay/PayOS">💳 Sepay</span>
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500">
                            {{ $user->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="p-4 text-right">
                            <div class="inline-block text-left relative">
                                <button type="button" onclick="toggleActionDropdown(event, 'dropdown-{{ $user->id }}')" class="p-1.5 text-gray-400 hover:text-emerald-500 rounded-xl hover:bg-emerald-50/50 transition-all focus:outline-none border border-transparent hover:border-emerald-100">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                </button>
                                
                                <div id="dropdown-{{ $user->id }}" class="action-dropdown absolute right-0 mt-1.5 w-48 bg-white border border-gray-150 rounded-2xl shadow-xl z-30 hidden overflow-hidden divide-y divide-gray-50 text-left">
                                    <div class="py-1">
                                        <button type="button" onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}', {{ $user->is_active ? 1 : 0 }}, {{ $user->max_licenses }}, {{ $user->hasPermission('can_create_license') ? 1 : 0 }}, {{ $user->hasPermission('can_manage_devices') ? 1 : 0 }}, {{ $user->hasPermission('can_use_sepay') ? 1 : 0 }})" class="w-full text-left px-4 py-2.5 text-xs font-bold text-cyan-600 hover:bg-cyan-50 transition-colors flex items-center space-x-2">
                                            <span>✏️</span>
                                            <span>Sửa &amp; Phân quyền</span>
                                        </button>
                                    </div>
                                    @if($user->id !== auth()->id())
                                    <div class="py-1">
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="w-full" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors flex items-center space-x-2">
                                                <span>🗑️</span>
                                                <span>Xóa người dùng</span>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400">Không có dữ liệu người dùng</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $users->links() }}
</div>

<!-- ================= MODALS ================= -->

<!-- Create User Modal -->
<div id="create-user-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4 my-8">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">Thêm Người dùng mới</h3>
            <button onclick="closeModal('create-user-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Họ và tên</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Mật khẩu</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Vai trò (Role)</label>
                    <select name="role" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                        <option value="user">User thường</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Trạng thái</label>
                    <select name="is_active" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                        <option value="1">Hoạt động</option>
                        <option value="0">Tạm khóa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Giới hạn số lượng Key tối đa <span class="text-gray-400 font-normal">(0 = Không giới hạn)</span></label>
                <input type="number" name="max_licenses" value="0" min="0" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Phân quyền (Permissions)</label>
                <div class="space-y-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="perm_create_license" value="1" checked class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span>🔑 Cho phép tạo Giấy phép (Key)</span>
                    </label>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="perm_manage_devices" value="1" checked class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span>🖥️ Cho phép quản lý thiết bị (Unlink/Block)</span>
                    </label>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="perm_use_sepay" value="1" checked class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span>💳 Cho phép kết nối cổng Sepay/PayOS riêng</span>
                    </label>
                </div>
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('create-user-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">Hủy</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4 my-8">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">Sửa thông tin Người dùng</h3>
            <button onclick="closeModal('edit-user-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit-user-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Họ và tên</label>
                <input type="text" id="edit-name" name="name" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" id="edit-email" name="email" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Mật khẩu mới <span class="text-gray-400 font-normal">(để trống nếu không đổi)</span></label>
                <input type="password" name="password" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" placeholder="••••••••">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Vai trò (Role)</label>
                    <select id="edit-role" name="role" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                        <option value="user">User thường</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Trạng thái</label>
                    <select id="edit-is-active" name="is_active" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                        <option value="1">Hoạt động</option>
                        <option value="0">Tạm khóa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Giới hạn số lượng Key tối đa <span class="text-gray-400 font-normal">(0 = Không giới hạn)</span></label>
                <input type="number" id="edit-max-licenses" name="max_licenses" min="0" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Phân quyền (Permissions)</label>
                <div class="space-y-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" id="edit-perm-create" name="perm_create_license" value="1" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span>🔑 Cho phép tạo Giấy phép (Key)</span>
                    </label>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" id="edit-perm-manage" name="perm_create_license" value="1" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span>🖥️ Cho phép quản lý thiết bị (Unlink/Block)</span>
                    </label>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" id="edit-perm-sepay" name="perm_create_license" value="1" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        <span>💳 Cho phép kết nối cổng Sepay/PayOS riêng</span>
                    </label>
                </div>
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('edit-user-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">Hủy</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditModal(id, name, email, role, isActive, maxLicenses, permCreate, permManage, permSepay) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = role;
        document.getElementById('edit-is-active').value = isActive;
        document.getElementById('edit-max-licenses').value = maxLicenses;
        document.getElementById('edit-perm-create').checked = permCreate;
        document.getElementById('edit-perm-manage').checked = permManage;
        document.getElementById('edit-perm-sepay').checked = permSepay;
        document.getElementById('edit-user-form').action = '/admin/users/' + id + '/update';
        openModal('edit-user-modal');
    }

    function toggleActionDropdown(event, dropdownId) {
        event.stopPropagation();
        document.querySelectorAll('.action-dropdown').forEach(el => {
            if (el.id !== dropdownId) {
                el.classList.add('hidden');
            }
        });
        const target = document.getElementById(dropdownId);
        target.classList.toggle('hidden');
    }

    document.addEventListener('click', function() {
        document.querySelectorAll('.action-dropdown').forEach(el => {
            el.classList.add('hidden');
        });
    });
</script>
@endsection
