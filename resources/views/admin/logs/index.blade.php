@extends('admin.layouts.app')

@section('title', __('messages.logs') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.logs'))

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 mb-6">
    <!-- Search and Filter Form -->
    <form action="{{ route('admin.logs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_logs') }}" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm w-full sm:w-64">
        
        <select name="type" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
            <option value="">{{ __('messages.all_log_types') }}</option>
            <option value="crash" {{ request('type') === 'crash' ? 'selected' : '' }}>Crash</option>
            <option value="ffmpeg_error" {{ request('type') === 'ffmpeg_error' ? 'selected' : '' }}>FFmpeg Error</option>
            <option value="login" {{ request('type') === 'login' ? 'selected' : '' }}>Login</option>
            <option value="activate" {{ request('type') === 'activate' ? 'selected' : '' }}>Activate</option>
            <option value="update" {{ request('type') === 'update' ? 'selected' : '' }}>Update</option>
            <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
        </select>

        <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-2xl border border-emerald-500 text-emerald-600 bg-white hover:bg-emerald-50 transition-colors">
            {{ __('messages.filter') }}
        </button>
    </form>

    @if(auth()->user()->isSuperAdmin())
    <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa toàn bộ nhật ký hệ thống?')">
        @csrf
        <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-2xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center space-x-1">
            <span>🗑️</span>
            <span>Xóa toàn bộ nhật ký</span>
        </button>
    </form>
    @endif
</div>

<!-- Logs Table Card -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold bg-gray-50/50">
                    <th class="p-4 w-1/5">{{ __('messages.timestamp') }}</th>
                    <th class="p-4 w-1/8">{{ __('messages.type') }}</th>
                    <th class="p-4 w-1/4">Device ID</th>
                    <th class="p-4 w-2/5">{{ __('messages.message') }}</th>
                    <th class="p-4 text-right">{{ __('messages.details') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4 text-xs text-gray-500 font-semibold">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="p-4">
                            @if($log->type === 'crash' || $log->type === 'ffmpeg_error')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">{{ $log->type }}</span>
                            @elseif($log->type === 'activate' || $log->type === 'payment')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ $log->type }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700">{{ $log->type }}</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($log->device_id)
                                <a href="{{ route('admin.devices.index', ['search' => $log->device_id]) }}" class="font-mono text-xs text-emerald-600 hover:text-emerald-700 hover:underline block truncate max-w-[200px]">{{ $log->device_id }}</a>
                            @else
                                <span class="text-gray-400 italic">System</span>
                            @endif
                        </td>
                        <td class="p-4 font-medium text-gray-700">
                            {{ $log->message }}
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end space-x-1.5">
                                @if($log->details)
                                    <button onclick="viewDetailsModal('{{ addslashes($log->message) }}', '{{ json_encode($log->details) }}')" class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">{{ __('messages.view_details') }}</button>
                                @else
                                    <span class="text-gray-400 text-xs italic">{{ __('messages.no_details') }}</span>
                                @endif
                                <form action="{{ route('admin.logs.destroy', $log->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dòng nhật ký này?')">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-xl transition-colors" title="Xóa dòng log">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $logs->links() }}
</div>

<!-- ================= LOG DETAILS MODAL ================= -->
<div id="details-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-50 pb-2">
            <h3 id="details-title" class="font-extrabold text-lg text-gray-900 truncate">{{ __('messages.view_details') }}</h3>
            <button onclick="closeModal('details-modal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('messages.raw_json') }}</label>
            <pre id="details-json" class="w-full p-4 rounded-2xl bg-gray-50 font-mono text-xs text-gray-700 overflow-auto max-h-96 whitespace-pre-wrap"></pre>
        </div>

        <div class="flex pt-2">
            <button type="button" onclick="closeModal('details-modal')" class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function viewDetailsModal(message, jsonStr) {
        document.getElementById('details-title').innerText = message;
        try {
            const parsed = JSON.parse(jsonStr);
            document.getElementById('details-json').innerText = JSON.stringify(parsed, null, 4);
        } catch (e) {
            document.getElementById('details-json').innerText = jsonStr;
        }
        document.getElementById('details-modal').classList.remove('hidden');
    }
</script>
@endsection
