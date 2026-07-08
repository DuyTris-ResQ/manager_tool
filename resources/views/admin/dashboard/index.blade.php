@extends('admin.layouts.app')

@section('title', __('messages.dashboard') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.dashboard'))

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Revenue -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
        <div class="p-3.5 bg-emerald-50 text-emerald-500 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('messages.total_revenue') }}</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalRevenue) }} VND</p>
        </div>
    </div>

    <!-- Online Devices -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
        <div class="p-3.5 bg-cyan-50 text-cyan-500 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('messages.online_devices') }}</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ $onlineDevices }} / {{ $totalDevices }}</p>
        </div>
    </div>

    <!-- Active Licenses -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
        <div class="p-3.5 bg-teal-50 text-teal-500 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('messages.active_licenses') }}</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ $activeLicenses }} / {{ $totalLicenses }}</p>
        </div>
    </div>

    <!-- Trial Licenses -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center space-x-4">
        <div class="p-3.5 bg-amber-50 text-amber-500 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('messages.trial_licenses') }}</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ $trialLicenses }}</p>
        </div>
    </div>

</div>

<!-- Split Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Recent Devices -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.recent_devices') }}</h3>
            <a href="{{ route('admin.devices.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">{{ __('messages.view_all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold">
                        <th class="pb-3">{{ __('messages.computer') }}</th>
                        <th class="pb-3">{{ __('messages.specs') }}</th>
                        <th class="pb-3">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($recentDevices as $device)
                        <tr>
                            <td class="py-3.5">
                                <span class="font-bold text-gray-800">{{ $device->computer_name }}</span>
                                <span class="block text-xs text-gray-400 mt-0.5">ID: {{ substr($device->device_id, 0, 10) }}...</span>
                            </td>
                            <td class="py-3.5 text-xs">
                                <span class="block text-gray-600 font-medium">CPU: {{ substr($device->cpu ?? 'N/A', 0, 20) }}</span>
                                <span class="block text-gray-400">GPU: {{ substr($device->gpu ?? 'N/A', 0, 20) }}</span>
                            </td>
                            <td class="py-3.5">
                                @if($device->is_online)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Online</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-500">Offline</span>
                                @endif
                                <span class="block text-[10px] text-gray-400 mt-0.5">{{ $device->last_online ? $device->last_online->diffForHumans() : 'Never' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-400 text-sm">{{ __('messages.no_devices') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.recent_payments') }}</h3>
            <a href="{{ route('admin.payments.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">{{ __('messages.view_all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold">
                        <th class="pb-3">{{ __('messages.order_code') }}</th>
                        <th class="pb-3">{{ __('messages.amount') }}</th>
                        <th class="pb-3">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($recentPayments as $payment)
                        <tr>
                            <td class="py-3.5">
                                <span class="font-bold text-gray-800">#{{ $payment->order_code }}</span>
                                <span class="block text-[10px] text-gray-400 mt-0.5">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                            </td>
                            <td class="py-3.5">
                                <span class="font-bold text-gray-900">{{ number_format($payment->amount) }} VND</span>
                                <span class="block text-xs text-gray-400 mt-0.5 uppercase">{{ $payment->provider }}</span>
                            </td>
                            <td class="py-3.5">
                                @if($payment->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ __('messages.success') }}</span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">{{ __('messages.pending') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">{{ __('messages.failed') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-400 text-sm">{{ __('messages.no_payments') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Recent Logs Full Width -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mt-8 overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-extrabold text-lg text-gray-900">{{ __('messages.recent_logs') }}</h3>
        <a href="{{ route('admin.logs.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">{{ __('messages.view_all') }}</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold">
                    <th class="pb-3 w-1/4">{{ __('messages.timestamp') }}</th>
                    <th class="pb-3 w-1/6">{{ __('messages.type') }}</th>
                    <th class="pb-3 w-1/4">Device ID</th>
                    <th class="pb-3 w-1/3">{{ __('messages.message') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($recentLogs as $log)
                    <tr>
                        <td class="py-3.5 text-xs text-gray-500 font-medium">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3.5">
                            @if($log->type === 'crash' || $log->type === 'ffmpeg_error')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">{{ $log->type }}</span>
                            @elseif($log->type === 'activate' || $log->type === 'payment')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ $log->type }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700">{{ $log->type }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-xs font-mono text-gray-600">{{ $log->device_id ? substr($log->device_id, 0, 15) . '...' : 'System' }}</td>
                        <td class="py-3.5 text-gray-700 font-medium">{{ $log->message }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400 text-sm">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
