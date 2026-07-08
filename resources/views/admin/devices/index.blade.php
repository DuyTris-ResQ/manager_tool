@extends('admin.layouts.app')

@section('title', __('messages.devices') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.devices'))

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 mb-6">
    <!-- Search Form -->
    <form action="{{ route('admin.devices.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_devices') }}" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm w-full sm:w-64">
        
        <select name="online" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
            <option value="">{{ __('messages.all_connections') }}</option>
            <option value="1" {{ request('online') === '1' ? 'selected' : '' }}>{{ __('messages.online_only') }}</option>
            <option value="0" {{ request('online') === '0' ? 'selected' : '' }}>{{ __('messages.offline_only') }}</option>
        </select>

        <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-2xl border border-emerald-500 text-emerald-600 bg-white hover:bg-emerald-50 transition-colors">
            {{ __('messages.filter') }}
        </button>
    </form>
</div>

<!-- Devices Table Card -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold bg-gray-50/50">
                    <th class="p-4">{{ __('messages.device_info') }}</th>
                    <th class="p-4">{{ __('messages.specs') }}</th>
                    <th class="p-4">{{ __('messages.connection_version') }}</th>
                    <th class="p-4">{{ __('messages.assigned_license') }}</th>
                    <th class="p-4 text-right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($devices as $device)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4">
                            <span class="font-bold text-gray-800 block text-base">{{ $device->computer_name }}</span>
                            <span class="font-mono text-[10px] text-gray-400 block mt-0.5 select-all" title="{{ $device->device_id }}">
                                HWID: <span class="bg-gray-50 px-1 py-0.5 rounded border border-gray-100 hover:border-gray-250 cursor-pointer">{{ strlen($device->device_id) > 16 ? substr($device->device_id, 0, 8) . '...' . substr($device->device_id, -8) : $device->device_id }}</span>
                            </span>
                        </td>
                        <td class="p-4 text-xs space-y-1">
                            <div><span class="text-gray-400 font-semibold uppercase">CPU:</span> <span class="text-gray-700 font-medium">{{ strlen($device->cpu) > 40 ? substr($device->cpu, 0, 40) . '...' : ($device->cpu ?: 'N/A') }}</span></div>
                            <div><span class="text-gray-400 font-semibold uppercase">GPU:</span> <span class="text-gray-700 font-medium">{{ strlen($device->gpu) > 40 ? substr($device->gpu, 0, 40) . '...' : ($device->gpu ?: 'N/A') }}</span></div>
                            <div><span class="text-gray-400 font-semibold uppercase">OS:</span> <span class="text-gray-600">{{ $device->os ?: 'N/A' }}</span></div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center space-x-1.5">
                                @if($device->is_online)
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-xs font-bold text-emerald-700">Online</span>
                                @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                                    <span class="text-xs font-medium text-gray-500">Offline</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 mt-1">IP: {{ $device->ip ?: 'N/A' }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">Version: <span class="font-bold text-gray-600">{{ $device->app_version ?: '1.0.0' }}</span></div>
                        </td>
                        <td class="p-4">
                            @if($device->license)
                                <div class="flex items-center space-x-1.5">
                                    <button type="button" onclick="openQuickEditModal({{ $device->license->id }}, '{{ $device->license->license_key }}', '{{ $device->license->status }}', {{ $device->license->max_devices }})" class="font-mono font-bold text-emerald-600 hover:text-emerald-700 hover:underline text-left block tracking-wider">
                                        {{ $device->license->license_key }}
                                    </button>
                                    <button type="button" onclick="openQuickEditModal({{ $device->license->id }}, '{{ $device->license->license_key }}', '{{ $device->license->status }}', {{ $device->license->max_devices }})" class="p-1 text-gray-400 hover:text-emerald-500 rounded-lg hover:bg-emerald-50/50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </div>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-gray-100 text-gray-600 uppercase">
                                    @if($device->license->status === 'active')
                                        {{ __('messages.active') }}
                                    @elseif($device->license->status === 'trial')
                                        {{ __('messages.trial') }}
                                    @elseif($device->license->status === 'expired')
                                        {{ __('messages.expired') }}
                                    @elseif($device->license->status === 'disabled')
                                        {{ __('messages.disabled') }}
                                    @else
                                        {{ __('messages.banned') }}
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 italic">{{ __('messages.unlicensed') }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="inline-block text-left relative">
                                <button type="button" onclick="toggleActionDropdown(event, 'dropdown-{{ $device->id }}')" class="p-1.5 text-gray-400 hover:text-emerald-500 rounded-xl hover:bg-emerald-50/50 transition-all focus:outline-none border border-transparent hover:border-emerald-100">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                </button>
                                
                                <div id="dropdown-{{ $device->id }}" class="action-dropdown absolute right-0 mt-1.5 w-44 bg-white border border-gray-150 rounded-2xl shadow-xl z-30 hidden overflow-hidden divide-y divide-gray-50">
                                    <div class="py-1">
                                        @if($device->license_id)
                                            <form action="{{ route('admin.devices.remove', $device->id) }}" method="POST" class="w-full">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-amber-600 hover:bg-amber-50 hover:text-amber-700 transition-colors flex items-center space-x-2">
                                                    <span>🔓</span>
                                                    <span>{{ __('messages.remove_license') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.devices.block', $device->id) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to block/remove this device?')">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 hover:text-red-750 transition-colors flex items-center space-x-2">
                                                <span>🚫</span>
                                                <span>{{ __('messages.block_device') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">{{ __('messages.no_devices') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $devices->links() }}
</div>

<!-- Quick Edit License Modal -->
<div id="quick-edit-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.extend') }} / {{ __('messages.limit') }} / {{ __('messages.status') }}</h3>
            <button onclick="closeModal('quick-edit-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="text-sm text-gray-500">License: <span id="quick-edit-license-key" class="font-mono font-bold text-gray-800"></span></p>

        <form id="quick-edit-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.status') }}</label>
                <select id="quick-edit-status" name="status" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
                    <option value="active">{{ __('messages.active') }}</option>
                    <option value="trial">{{ __('messages.trial') }}</option>
                    <option value="expired">{{ __('messages.expired') }}</option>
                    <option value="disabled">{{ __('messages.disabled') }}</option>
                    <option value="banned">{{ __('messages.banned') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.devices_limit') }}</label>
                <input type="number" id="quick-edit-max-devices" name="max_devices" min="1" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.duration_days') }} ({{ __('messages.extend') }})</label>
                <input type="number" name="extend_days" placeholder="0 (No extension)" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
                <p class="text-[10px] text-gray-400 mt-1">Leave blank or set to 0 if you do not want to add days.</p>
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeModal('quick-edit-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">{{ __('messages.cancel') }}</button>
                <button type="submit" class="flex-1 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 rounded-2xl shadow-md transition-all">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openQuickEditModal(id, key, status, maxDevices) {
        document.getElementById('quick-edit-license-key').innerText = key;
        document.getElementById('quick-edit-status').value = status;
        document.getElementById('quick-edit-max-devices').value = maxDevices;
        document.getElementById('quick-edit-form').action = '/admin/licenses/' + id + '/quick-update';
        document.getElementById('quick-edit-modal').classList.remove('hidden');
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
</script>
@endsection
