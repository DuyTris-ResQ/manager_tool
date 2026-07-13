@extends('admin.layouts.app')

@section('title', __('messages.settings') . ' - ' . __('messages.settings'))
@section('page_title', __('messages.settings'))

@section('content')
<!-- Tab Navigation Header -->
<div class="flex space-x-2 p-1.5 bg-slate-150/60 rounded-2xl w-full md:w-max mb-6">
    <button type="button" id="btn-tab-system" onclick="switchTab('system')"
        class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm bg-emerald-500 text-white">
        ⚙️ Cài đặt hệ thống
    </button>
    <button type="button" id="btn-tab-billing" onclick="switchTab('billing')"
        class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 text-slate-655 hover:bg-slate-100">
        💳 Thanh toán &amp; Liên hệ
    </button>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8 w-full">
    @csrf

    <!-- ════════════════════ TAB 1: SYSTEM SETTINGS ════════════════════ -->
    <div id="panel-tab-system" class="space-y-8">
        
        <!-- Core configurations (2-column layout) -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6">
            <h3 class="font-extrabold text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ __('messages.settings') }}</span>
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column: Inputs -->
                <div class="space-y-6">
                    <!-- Maintenance Mode -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2.5">{{ __('messages.maintenance_mode') }}</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="maintenance" value="0" {{ $settings['maintenance'] === '0' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500/20">
                                <span class="text-sm font-semibold text-gray-700">{{ __('messages.maintenance_disabled') }}</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="maintenance" value="1" {{ $settings['maintenance'] === '1' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500/20">
                                <span class="text-sm font-semibold text-red-600">{{ __('messages.maintenance_enabled') }}</span>
                            </label>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5">{{ __('messages.maintenance_help') }}</p>
                    </div>

                    <!-- Minimum Version & Trial Days stacked -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.minimum_version') }}</label>
                            <input type="text" name="minimum_version" value="{{ $settings['minimum_version'] }}" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
                            <p class="text-[9px] text-gray-400 mt-1">{{ __('messages.min_version_help') }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.trial_days') }}</label>
                            <input type="number" name="trial_days" value="{{ $settings['trial_days'] }}" min="1" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
                            <p class="text-[9px] text-gray-400 mt-1">{{ __('messages.trial_days_help') }}</p>
                        </div>
                    </div>

                    <!-- Heartbeat Interval -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.heartbeat_interval') }}</label>
                        <input type="number" name="heartbeat_interval" value="{{ $settings['heartbeat_interval'] }}" min="30" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm">
                        <p class="text-[10px] text-gray-400 mt-1.5">{{ __('messages.heartbeat_help') }}</p>
                    </div>
                </div>

                <!-- Right Column: Notice Area -->
                <div class="flex flex-col h-full justify-between">
                    <div class="flex-1 flex flex-col">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.notice') }}</label>
                        <textarea name="notice" class="flex-1 w-full min-h-[140px] px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" style="resize: none;">{{ $settings['notice'] }}</textarea>
                        <p class="text-[10px] text-gray-400 mt-1.5">{{ __('messages.notice_help') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Telegram Notifications -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6">
            <h3 class="font-extrabold text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>{{ __('messages.telegram_settings') }}</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.telegram_bot') }}</label>
                    <input type="password" name="telegram_bot_token" value="{{ $settings['telegram_bot_token'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="123456789:AAF-abcdef...">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.telegram_chat') }}</label>
                    <input type="text" name="telegram_chat_id" value="{{ $settings['telegram_chat_id'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="-100123456789">
                </div>
            </div>
            <p class="text-xs text-gray-400">{{ __('messages.telegram_help') }}</p>
        </div>
    </div>

    <!-- ════════════════════ TAB 2: BILLING & CONTACT ════════════════════ -->
    <div id="panel-tab-billing" class="space-y-8 hidden">
        
        <!-- Default Gateway Selector -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-extrabold text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Cổng thanh toán mặc định</span>
            </h3>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Chọn cổng thanh toán hiển thị cho khách hàng</label>
                <div class="flex flex-col sm:flex-row gap-4">
                    <label class="flex items-center space-x-2.5 p-3 rounded-2xl border border-gray-150 cursor-pointer hover:bg-slate-50 flex-1">
                        <input type="radio" name="payment_gateway" value="vietqr_only" {{ $settings['payment_gateway'] === 'vietqr_only' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500/20">
                        <div>
                            <p class="text-sm font-bold text-slate-800">VietQR truyền thống</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Khách tự chuyển khoản thủ công bằng quét QR.</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center space-x-2.5 p-3 rounded-2xl border border-gray-150 cursor-pointer hover:bg-slate-50 flex-1">
                        <input type="radio" name="payment_gateway" value="payos" {{ $settings['payment_gateway'] === 'payos' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500/20">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Cổng PayOS</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Tạo link thanh toán qua PayOS tự động.</p>
                        </div>
                    </label>

                    <label class="flex items-center space-x-2.5 p-3 rounded-2xl border border-gray-150 cursor-pointer hover:bg-slate-50 flex-1">
                        <input type="radio" name="payment_gateway" value="sepay" {{ $settings['payment_gateway'] === 'sepay' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500/20">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Cổng SePay</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Tạo link checkout thông qua cổng SePay.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Row: Bank Settings, PayOS Settings, SePay Settings (3 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Banking configuration (VietQR BIN + STK) -->
            <div id="config-vietqr-card" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6 flex flex-col justify-between">
                <div class="space-y-6">
                    <h3 class="font-black text-sm md:text-[15px] text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        <span>{{ __('messages.bank_settings') }}</span>
                    </h3>

                    <div class="space-y-4">
                        <!-- Bank Search Combobox -->
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.bank_name') }}</label>
                            <input type="hidden" id="bank-select-val" name="bank_name" value="{{ $settings['bank_name'] }}">
                            <input type="hidden" id="bank-bin-val" name="bank_bin" value="{{ $settings['bank_bin'] }}">
                            
                            <div id="bank-dropdown-trigger" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus-within:ring-2 focus-within:ring-emerald-500/20 focus-within:border-emerald-500 text-sm bg-white font-semibold flex items-center justify-between cursor-pointer">
                                <div class="flex items-center space-x-2">
                                    <img id="selected-bank-logo" src="" class="w-6 h-6 object-contain hidden">
                                    <span id="selected-bank-text">Chọn ngân hàng...</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            
                            <div id="bank-dropdown-panel" class="absolute left-0 right-0 mt-2 bg-white border border-gray-150 shadow-xl rounded-2xl z-50 hidden p-3 space-y-2.5">
                                <div class="relative">
                                    <input type="text" id="bank-search-input" placeholder="Tìm kiếm ngân hàng..." class="w-full px-3.5 py-2 pl-9 bg-gray-50 rounded-xl border border-gray-150 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                
                                <div id="bank-options-list" class="max-h-60 overflow-y-auto space-y-1 pr-1">
                                    <!-- Options populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Account Number -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.bank_account') }}</label>
                            <input type="text" name="bank_account" value="{{ $settings['bank_account'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold" placeholder="Ví dụ: 0968686868">
                        </div>

                        <!-- Account Holder -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.bank_holder') }}</label>
                            <input type="text" name="bank_holder" value="{{ $settings['bank_holder'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold uppercase" placeholder="Ví dụ: NGUYEN VAN A">
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">{{ __('messages.bank_help') }}</p>
            </div>

            <!-- PayOS Credentials -->
            <div id="config-payos-card" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6 flex flex-col justify-between">
                <div class="space-y-6">
                    <h3 class="font-black text-sm md:text-[15px] text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>{{ __('messages.payos_settings') }}</span>
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.payos_client_id') }}</label>
                            <input type="text" name="payos_client_id" value="{{ $settings['payos_client_id'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="MOCK_CLIENT_ID">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.payos_api_key') }}</label>
                            <input type="password" name="payos_api_key" value="{{ $settings['payos_api_key'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="••••••••••••••••">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.payos_checksum_key') }}</label>
                            <input type="password" name="payos_checksum_key" value="{{ $settings['payos_checksum_key'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="••••••••••••••••">
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">Thông tin API Key để tạo liên kết thanh toán PayOS tự động.</p>
            </div>

            <!-- SePay Credentials -->
            <div id="config-sepay-card" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6 flex flex-col justify-between">
                <div class="space-y-6">
                    <h3 class="font-black text-sm md:text-[15px] text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Cài đặt SePay</span>
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">SePay Merchant ID</label>
                            <input type="text" name="sepay_merchant_id" value="{{ $settings['sepay_merchant_id'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="Vd: SP-TEST-XXXX">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">SePay API Secret Token</label>
                            <input type="password" name="sepay_api_key" value="{{ $settings['sepay_api_key'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-mono" placeholder="spsk_test_••••••••">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Môi trường (Environment)</label>
                            <select name="sepay_env" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white font-semibold">
                                <option value="sandbox" {{ $settings['sepay_env'] === 'sandbox' ? 'selected' : '' }}>Sandbox (Thử nghiệm)</option>
                                <option value="production" {{ $settings['sepay_env'] === 'production' ? 'selected' : '' }}>Production (Thực tế)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">Thông tin kết nối API để tích hợp cổng thanh toán SePay Checkout Form.</p>
            </div>
        </div>

        <!-- Pricing Packages – Configurable Slots -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-extrabold text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2 mb-5">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ __('messages.pricing_settings') }}</span>
            </h3>

            <div class="grid grid-cols-4 gap-4 mb-2 px-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                <div>Tên gói</div>
                <div>Thời hạn (ngày, 0=∞)</div>
                <div>Giá sale (₫)</div>
                <div>Giá gốc <span class="font-normal normal-case text-gray-300">(tùy chọn)</span></div>
            </div>

            @php
            $pkgColors = ['sky','violet','amber','emerald'];
            $pkgIcons  = ['🗓️','📆','🔥','♾️'];
            @endphp

            @foreach([1,2,3,4] as $n)
            @php $c = $pkgColors[$n-1]; $icon = $pkgIcons[$n-1]; @endphp
            <div class="grid grid-cols-4 gap-4 items-center py-3 {{ $n < 4 ? 'border-b border-gray-50' : '' }}">
                <!-- Label -->
                <div>
                    <div class="flex items-center space-x-2 mb-1.5">
                        <span class="text-base">{{ $icon }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Gói {{ $n }}</span>
                    </div>
                    <input type="text" name="pkg_{{ $n }}_label" value="{{ $settings['pkg_'.$n.'_label'] }}" required maxlength="50" class="w-full px-3 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold">
                </div>
                <!-- Days -->
                <div>
                    <div class="mb-1.5 h-4"></div>
                    <input type="number" name="pkg_{{ $n }}_days" value="{{ $settings['pkg_'.$n.'_days'] }}" required min="0" class="w-full px-3 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold">
                </div>
                <!-- Sale price -->
                <div>
                    <div class="mb-1.5 h-4"></div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">₫</span>
                        <input type="number" name="pkg_{{ $n }}_price" value="{{ $settings['pkg_'.$n.'_price'] }}" required min="0" class="w-full pl-7 pr-3 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold">
                    </div>
                </div>
                <!-- Original price -->
                <div>
                    <div class="mb-1.5 h-4"></div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-300">₫</span>
                        <input type="number" name="pkg_{{ $n }}_price_original" value="{{ $settings['pkg_'.$n.'_price_original'] }}" min="0" class="w-full pl-7 pr-3 py-2.5 rounded-2xl border border-dashed border-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 text-sm text-gray-500" placeholder="Trống nếu không sale">
                    </div>
                </div>
            </div>
            @endforeach
            <p class="text-xs text-gray-400 mt-3">💡 Gói có Thời hạn = 0 sẽ tính là Vĩnh viễn (Lifetime) và hiển thị biểu tượng vô cực.</p>
        </div>

        <!-- Contact & Support Links -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6">
            <h3 class="font-extrabold text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Thông tin liên hệ hỗ trợ</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone Support -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Số điện thoại Hotline</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold" placeholder="Vd: 0988888888">
                </div>

                <!-- Zalo link/number -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Số Zalo hỗ trợ (hoặc ID)</label>
                    <input type="text" name="contact_zalo" value="{{ $settings['contact_zalo'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold" placeholder="Vd: 0988888888">
                </div>

                <!-- Facebook Page Link -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Liên kết Facebook Page</label>
                    <input type="url" name="contact_facebook" value="{{ $settings['contact_facebook'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" placeholder="https://facebook.com/trangcuaban">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Địa chỉ Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" placeholder="support@duytris.com">
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Trang web chính thức</label>
                    <input type="url" name="contact_website" value="{{ $settings['contact_website'] }}" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" placeholder="https://duytris.com">
                </div>

                <!-- Support note -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ghi chú hỗ trợ</label>
                    <textarea name="contact_note" rows="2" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm" placeholder="Vd: Vui lòng nhắn tin qua Zalo hoặc Fanpage để được hỗ trợ kích hoạt nhanh nhất.">{{ $settings['contact_note'] }}</textarea>
                </div>
            </div>
            <p class="text-xs text-gray-400">Các thông tin liên hệ này sẽ tự động xuất hiện ở phần chân trang kích hoạt phía client để hỗ trợ khách hàng.</p>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end pt-4">
        <button type="submit" class="px-8 py-3 text-sm font-bold rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 text-white hover:from-cyan-600 hover:to-emerald-600 shadow-md shadow-cyan-200/50 hover:shadow-cyan-300/50 transition-all active:scale-[0.98]">
            {{ __('messages.save_settings') }}
        </button>
    </div>
</form>

<script>
    function switchTab(tabId) {
        const btnSys = document.getElementById('btn-tab-system');
        const btnBill = document.getElementById('btn-tab-billing');
        const panelSys = document.getElementById('panel-tab-system');
        const panelBill = document.getElementById('panel-tab-billing');

        if (tabId === 'system') {
            btnSys.className = "flex-1 md:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm bg-emerald-500 text-white";
            btnBill.className = "flex-1 md:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 text-slate-655 hover:bg-slate-100";
            panelSys.classList.remove('hidden');
            panelBill.classList.add('hidden');
        } else {
            btnBill.className = "flex-1 md:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm bg-emerald-500 text-white";
            btnSys.className = "flex-1 md:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 text-slate-655 hover:bg-slate-100";
            panelBill.classList.remove('hidden');
            panelSys.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('bank-dropdown-trigger');
        const panel = document.getElementById('bank-dropdown-panel');
        const searchInput = document.getElementById('bank-search-input');
        const optionsList = document.getElementById('bank-options-list');
        const hiddenInput = document.getElementById('bank-select-val');
        const hiddenBin = document.getElementById('bank-bin-val');
        const selectedLogo = document.getElementById('selected-bank-logo');
        const selectedText = document.getElementById('selected-bank-text');
        
        let allBanks = [];

        // Toggle panel
        trigger.addEventListener('click', function(e) {
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                searchInput.focus();
            }
            e.stopPropagation();
        });

        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            if (!panel.contains(e.target) && !trigger.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });

        // Filter banks on search
        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();
            renderOptions(query);
        });

        function selectBank(code, shortName, logoUrl, bin) {
            hiddenInput.value = code;
            hiddenBin.value = bin;
            selectedText.innerText = `${code} - ${shortName}`;
            selectedLogo.src = logoUrl;
            selectedLogo.classList.remove('hidden');
            panel.classList.add('hidden');
        }

        window.selectBank = selectBank;

        function renderOptions(query = '') {
            optionsList.innerHTML = '';
            const filtered = allBanks.filter(bank => {
                const searchStr = `${bank.code} ${bank.shortName} ${bank.name}`.toLowerCase();
                return searchStr.includes(query);
            });

            if (filtered.length === 0) {
                optionsList.innerHTML = '<p class="text-xs text-gray-400 p-3 text-center">Không tìm thấy ngân hàng</p>';
                return;
            }

            filtered.forEach(bank => {
                const item = document.createElement('div');
                item.className = 'flex items-center space-x-3 px-3 py-2.5 hover:bg-emerald-50/50 rounded-xl cursor-pointer transition-colors';
                item.setAttribute('onclick', `selectBank('${bank.code}', '${bank.shortName}', '${bank.logo}', '${bank.bin}')`);
                
                item.innerHTML = `
                    <img src="${bank.logo}" class="w-8 h-8 object-contain rounded-lg bg-white p-0.5 border border-gray-100 shrink-0">
                    <div class="text-left overflow-hidden">
                        <p class="text-xs font-bold text-gray-800">${bank.code} - ${bank.shortName}</p>
                        <p class="text-[9px] text-gray-450 truncate max-w-[280px]">${bank.name}</p>
                    </div>
                `;
                optionsList.appendChild(item);
            });
        }

        // Fetch banks
        fetch('https://api.vietqr.io/v2/banks')
            .then(response => response.json())
            .then(payload => {
                if (payload.code === '00' && Array.isArray(payload.data)) {
                    allBanks = payload.data;
                    renderOptions();
                    
                    // Set initial active state based on saved code
                    const saved = hiddenInput.value;
                    if (saved) {
                        const matched = allBanks.find(b => b.code === saved || b.bin === saved);
                        if (matched) {
                            selectBank(matched.code, matched.shortName, matched.logo, matched.bin);
                        }
                    }
                }
            })
            .catch(err => {
                console.error('Failed to load banks from VietQR API:', err);
            });

        // ════════════════════ PAYMENT GATEWAY TOGGLE ════════════════════
        const gatewayRadios = document.querySelectorAll('input[name="payment_gateway"]');
        const vietqrCard = document.getElementById('config-vietqr-card');
        const payosCard = document.getElementById('config-payos-card');
        const sepayCard = document.getElementById('config-sepay-card');

        function toggleGatewayConfigs() {
            const activeVal = document.querySelector('input[name="payment_gateway"]:checked').value;
            
            // Hide all first
            vietqrCard.style.display = 'none';
            payosCard.style.display = 'none';
            sepayCard.style.display = 'none';

            // Show matching card
            if (activeVal === 'vietqr_only') {
                vietqrCard.style.display = 'flex';
            } else if (activeVal === 'payos') {
                payosCard.style.display = 'flex';
            } else if (activeVal === 'sepay') {
                sepayCard.style.display = 'flex';
                vietqrCard.style.display = 'flex'; // Hiển thị cấu hình ngân hàng cho SePay để tạo QR
            }
        }

        gatewayRadios.forEach(radio => {
            radio.addEventListener('change', toggleGatewayConfigs);
        });

        // Initialize visibility
        toggleGatewayConfigs();
    });
</script>
@endsection
