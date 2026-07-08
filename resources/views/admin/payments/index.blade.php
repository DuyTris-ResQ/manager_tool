@extends('admin.layouts.app')

@section('title', __('messages.payment_history') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.payment_history'))

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 mb-6">
    <!-- Search and Filter Form -->
    <form action="{{ route('admin.payments.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_payment') }}" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm w-full sm:w-64">
        
        <select name="status" class="px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white">
            <option value="">{{ __('messages.all_statuses') }}</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('messages.failed') }}</option>
        </select>

        <button type="submit" class="px-5 py-2.5 text-sm font-bold rounded-2xl border border-emerald-500 text-emerald-600 bg-white hover:bg-emerald-50 transition-colors">
            {{ __('messages.filter') }}
        </button>
    </form>
</div>

<!-- Payments Table Card -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase font-bold bg-gray-50/50">
                    <th class="p-4">{{ __('messages.order_details') }}</th>
                    <th class="p-4">{{ __('messages.licenses') }}</th>
                    <th class="p-4">{{ __('messages.payment_provider') }}</th>
                    <th class="p-4">{{ __('messages.amount') }}</th>
                    <th class="p-4">{{ __('messages.transaction_code') }}</th>
                    <th class="p-4">{{ __('messages.status_paid_time') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4">
                            <span class="font-bold text-gray-800 block">Order #{{ $payment->order_code }}</span>
                            <span class="text-xs text-gray-400 mt-0.5 block">{{ __('messages.created') }}: {{ $payment->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td class="p-4">
                            @if($payment->license)
                                <a href="{{ route('admin.licenses.index', ['search' => $payment->license->license_key]) }}" class="font-mono font-bold text-emerald-600 hover:text-emerald-700 hover:underline tracking-wider">{{ $payment->license->license_key }}</a>
                            @else
                                <span class="text-gray-400 italic">Deleted License</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 uppercase">{{ $payment->provider }}</span>
                        </td>
                        <td class="p-4 font-extrabold text-gray-900">
                            {{ number_format($payment->amount) }} VND
                        </td>
                        <td class="p-4 font-mono text-xs text-gray-500 font-semibold">
                            {{ $payment->transaction_code ?: 'N/A' }}
                        </td>
                        <td class="p-4">
                            <div>
                                @if($payment->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ __('messages.completed') }}</span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">{{ __('messages.pending') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 font-bold">{{ __('messages.failed') }}</span>
                                @endif
                            </div>
                            @if($payment->paid_at)
                                <span class="text-xs text-gray-400 mt-1 block">{{ __('messages.paid') }}: {{ $payment->paid_at->format('Y-m-d H:i:s') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">{{ __('messages.no_payments') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $payments->links() }}
</div>
@endsection
