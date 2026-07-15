@extends('admin.layouts.app')

@section('title', __('messages.licenses') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.licenses'))

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 mb-6">
    <!-- Search and Filter Form -->
    <form action="{{ route('admin.licenses.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_licenses') }}" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm w-full sm:w-64">
        
        <select name="status" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
            <option value="">{{ __('messages.all_statuses') }}</option>
            <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>{{ __('messages.trial') }}</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('messages.expired') }}</option>
            <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>{{ __('messages.disabled') }}</option>
            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>{{ __('messages.banned') }}</option>
        </select>

        <select name="product_name" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
            <option value="">{{ __('Tất cả ứng dụng') }}</option>
            <option value="default" {{ request('product_name') === 'default' ? 'selected' : '' }}>Dùng chung (Không chỉ định)</option>
            @foreach($products as $prod)
                <option value="{{ $prod }}" {{ request('product_name') === $prod ? 'selected' : '' }}>{{ $prod }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-2xl border border-emerald-500 text-emerald-600 bg-white hover:bg-emerald-50 transition-colors">
            {{ __('messages.filter') }}
        </button>
    </form>

    <!-- Create License Button -->
    <button onclick="openModal('create-license-modal')" class="px-6 py-2.5 text-sm font-bold rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 text-white hover:from-cyan-600 hover:to-emerald-600 shadow-md shadow-cyan-200/50 transition-all flex items-center justify-center space-x-1.5 self-start md:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>{{ __('messages.generate_license') }}</span>
    </button>
</div>

<!-- Bulk Actions Toolbar (Hidden by default, shown when items selected) -->
<div id="bulk-actions-toolbar" class="bg-emerald-50 border border-emerald-100 rounded-3xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 hidden">
    <div class="flex items-center space-x-2 text-sm text-emerald-800 font-medium">
        <span>🎉</span>
        <span>Đã chọn <strong id="selected-count" class="text-base text-emerald-600">0</strong> mục bản quyền.</span>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="submitBulkAction('export_txt')" class="px-4 py-2 text-xs font-bold rounded-2xl border border-emerald-300 text-emerald-700 bg-white hover:bg-emerald-100/50 transition-all flex items-center space-x-1">
            <span>📥</span> <span>Xuất TXT</span>
        </button>
        <button type="button" onclick="submitBulkAction('export_csv')" class="px-4 py-2 text-xs font-bold rounded-2xl border border-emerald-300 text-emerald-700 bg-white hover:bg-emerald-100/50 transition-all flex items-center space-x-1">
            <span>📊</span> <span>Xuất CSV</span>
        </button>
        <button type="button" onclick="submitBulkAction('unlink')" class="px-4 py-2 text-xs font-bold rounded-2xl border border-amber-300 text-amber-700 bg-white hover:bg-amber-100/50 transition-all flex items-center space-x-1">
            <span>🖥️</span> <span>Gỡ thiết bị</span>
        </button>
        <button type="button" onclick="submitBulkAction('delete')" class="px-4 py-2 text-xs font-bold rounded-2xl bg-rose-500 text-white hover:bg-rose-600 shadow-md shadow-rose-200 transition-all flex items-center space-x-1">
            <span>🗑️</span> <span>Xóa đã chọn</span>
        </button>
    </div>
</div>

<form id="bulk-action-form" action="{{ route('admin.licenses.bulk_action') }}" method="POST">
    @csrf
    <input type="hidden" id="bulk-action-type" name="action" value="">

    <!-- License Table Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold bg-gray-50/50">
                        <th class="p-4 w-10">
                            <input type="checkbox" id="select-all-licenses" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        </th>
                        <th class="p-4">{{ __('messages.licenses') }} Key</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th class="p-4">Người sở hữu</th>
                    @endif
                    <th class="p-4">Ứng dụng (App)</th>
                    <th class="p-4">{{ __('messages.status') }}</th>
                    <th class="p-4">{{ __('messages.max_devices') }}</th>
                    <th class="p-4">{{ __('messages.expires_at') }}</th>
                    <th class="p-4 text-right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($licenses as $license)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4">
                            <input type="checkbox" name="ids[]" value="{{ $license->id }}" class="license-checkbox rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                        </td>
                        <td class="p-4">
                            <span class="font-mono font-bold text-gray-800 tracking-wider block">{{ $license->license_key }}</span>
                            <span class="text-[10px] text-gray-400">{{ __('messages.created') }}: {{ $license->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        @if(auth()->user()->isSuperAdmin())
                        <td class="p-4">
                            @if($license->user)
                                <span class="font-semibold text-gray-800">{{ $license->user->name }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $license->user->email }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Hệ thống</span>
                            @endif
                        </td>
                        @endif
                        <td class="p-4 font-medium text-gray-700">
                            {{ $license->product_name ?: 'Tất cả ứng dụng' }}
                        </td>
                        <td class="p-4">
                            @if($license->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ __('messages.active') }}</span>
                            @elseif($license->status === 'trial')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700">{{ __('messages.trial') }}</span>
                            @elseif($license->status === 'expired')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">{{ __('messages.expired') }}</span>
                            @elseif($license->status === 'disabled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">{{ __('messages.disabled') }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">{{ __('messages.banned') }}</span>
                            @endif
                        </td>
                        <td class="p-4 font-semibold text-gray-700">
                            {{ $license->devices_count }} / {{ $license->max_devices }}
                        </td>
                        <td class="p-4">
                            <span class="font-medium block text-gray-700">{{ $license->expire_at ? $license->expire_at->format('Y-m-d') : 'Lifetime' }}</span>
                            @if($license->expire_at)
                                <span class="text-xs text-gray-400 mt-0.5 block">({{ $license->expire_at->diffForHumans() }})</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="inline-block text-left relative">
                                <button type="button" onclick="toggleActionDropdown(event, 'dropdown-{{ $license->id }}')" class="p-1.5 text-gray-400 hover:text-emerald-500 rounded-xl hover:bg-emerald-50/50 transition-all focus:outline-none border border-transparent hover:border-emerald-100">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                </button>
                                
                                <div id="dropdown-{{ $license->id }}" class="action-dropdown absolute right-0 mt-1.5 w-48 bg-white border border-gray-150 rounded-2xl shadow-xl z-30 hidden overflow-hidden divide-y divide-gray-50 text-left">
                                    <div class="py-1">
                                        <button type="button" onclick="openExtendModal({{ $license->id }}, '{{ $license->license_key }}')" class="w-full text-left px-4 py-2.5 text-xs font-bold text-emerald-600 hover:bg-emerald-50 transition-colors flex items-center space-x-2">
                                            <span>📅</span>
                                            <span>{{ __('messages.extend') }}</span>
                                        </button>
                                        
                                        <button type="button" onclick="openDevicesModal({{ $license->id }}, '{{ $license->license_key }}', {{ $license->max_devices }})" class="w-full text-left px-4 py-2.5 text-xs font-bold text-cyan-600 hover:bg-cyan-50 transition-colors flex items-center space-x-2">
                                            <span>📱</span>
                                            <span>{{ __('messages.limit') }}</span>
                                        </button>
                                        
                                        <button type="button" onclick="openStatusModal({{ $license->id }}, '{{ $license->status }}')" class="w-full text-left px-4 py-2.5 text-xs font-bold text-teal-600 hover:bg-teal-50 transition-colors flex items-center space-x-2">
                                            <span>⚙️</span>
                                            <span>{{ __('messages.status') }}</span>
                                        </button>
                                    </div>
                                    <div class="py-1">
                                        <form action="{{ route('admin.licenses.destroy', $license->id) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this license?')">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors flex items-center space-x-2">
                                                <span>🗑️</span>
                                                <span>{{ __('messages.delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-450">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</form>

<!-- Pagination -->
<div class="mt-4">
    {{ $licenses->links() }}
</div>

<!-- ================= MODALS ================= -->

<!-- Create License Modal -->
<div id="create-license-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.generate_license') }}</h3>
            <button onclick="closeModal('create-license-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.licenses.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.devices_limit') }}</label>
                <input type="number" name="max_devices" value="1" min="1" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.duration_days') }}</label>
                <input type="number" name="duration_days" value="30" min="1" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tên ứng dụng / App Name <span class="text-gray-400 font-normal">(để trống nếu dùng chung)</span></label>
                <input type="text" name="product_name" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" placeholder="Ví dụ: toolvideo">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.initial_status') }}</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                    <option value="active">{{ __('messages.active') }}</option>
                    <option value="trial">{{ __('messages.trial') }}</option>
                    <option value="disabled">{{ __('messages.disabled') }}</option>
                </select>
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Người sở hữu (Owner)</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                    <option value="">-- Hệ thống (Không gán) --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('create-license-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">{{ __('messages.cancel') }}</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Extend License Modal -->
<div id="extend-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.extend') }}</h3>
            <button onclick="closeModal('extend-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="text-sm text-gray-500">License: <span id="extend-license-key" class="font-mono font-bold text-gray-800"></span></p>

        <form id="extend-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.duration_days') }}</label>
                <input type="number" name="days" value="30" min="1" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('extend-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">{{ __('messages.cancel') }}</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">{{ __('messages.extend') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Devices Limit Modal -->
<div id="devices-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.devices_limit') }}</h3>
            <button onclick="closeModal('devices-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="text-sm text-gray-500">License: <span id="devices-license-key" class="font-mono font-bold text-gray-800"></span></p>

        <form id="devices-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.devices_limit') }}</label>
                <input type="number" id="devices-input" name="max_devices" min="1" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('devices-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">{{ __('messages.cancel') }}</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Status Modify Modal -->
<div id="status-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.status') }}</h3>
            <button onclick="closeModal('status-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="status-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.status') }}</label>
                <select id="status-select" name="status" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                    <option value="active">{{ __('messages.active') }}</option>
                    <option value="trial">{{ __('messages.trial') }}</option>
                    <option value="expired">{{ __('messages.expired') }}</option>
                    <option value="disabled">{{ __('messages.disabled') }}</option>
                    <option value="banned">{{ __('messages.banned') }}</option>
                </select>
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('status-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">{{ __('messages.cancel') }}</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">{{ __('messages.save') }}</button>
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

    function openExtendModal(id, key) {
        document.getElementById('extend-license-key').innerText = key;
        document.getElementById('extend-form').action = '/admin/licenses/' + id + '/extend';
        openModal('extend-modal');
    }

    function openDevicesModal(id, key, maxDevices) {
        document.getElementById('devices-license-key').innerText = key;
        document.getElementById('devices-input').value = maxDevices;
        document.getElementById('devices-form').action = '/admin/licenses/' + id + '/max-devices';
        openModal('devices-modal');
    }

    function openStatusModal(id, currentStatus) {
        document.getElementById('status-select').value = currentStatus;
        document.getElementById('status-form').action = '/admin/licenses/' + id + '/update';
        openModal('status-modal');
    }

    function toggleActionDropdown(event, dropdownId) {
        event.stopPropagation();
        // Close all other dropdowns
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

    // Bulk actions checkboxes handling
    const selectAllCheckbox = document.getElementById('select-all-licenses');
    const licenseCheckboxes = document.querySelectorAll('.license-checkbox');
    const bulkToolbar = document.getElementById('bulk-actions-toolbar');
    const selectedCountLabel = document.getElementById('selected-count');

    function updateBulkToolbar() {
        const checkedCount = document.querySelectorAll('.license-checkbox:checked').length;
        if (selectedCountLabel) {
            selectedCountLabel.innerText = checkedCount;
        }
        if (bulkToolbar) {
            if (checkedCount > 0) {
                bulkToolbar.classList.remove('hidden');
            } else {
                bulkToolbar.classList.add('hidden');
            }
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            licenseCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateBulkToolbar();
        });
    }

    licenseCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const total = licenseCheckboxes.length;
            const checked = document.querySelectorAll('.license-checkbox:checked').length;
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = (total === checked);
                selectAllCheckbox.indeterminate = (checked > 0 && checked < total);
            }
            updateBulkToolbar();
        });
    });

    function submitBulkAction(actionType) {
        const checkedCount = document.querySelectorAll('.license-checkbox:checked').length;
        if (checkedCount === 0) {
            alert('Vui lòng chọn ít nhất một bản quyền.');
            return;
        }

        let confirmMsg = '';
        if (actionType === 'delete') {
            confirmMsg = 'Bạn có chắc chắn muốn xóa ' + checkedCount + ' bản quyền đã chọn? Thiết bị đang liên kết sẽ bị gỡ ra.';
        } else if (actionType === 'unlink') {
            confirmMsg = 'Bạn có chắc chắn muốn gỡ thiết bị cho ' + checkedCount + ' bản quyền đã chọn?';
        }

        if (confirmMsg && !confirm(confirmMsg)) {
            return;
        }

        document.getElementById('bulk-action-type').value = actionType;
        document.getElementById('bulk-action-form').submit();
    }
</script>
@endsection
