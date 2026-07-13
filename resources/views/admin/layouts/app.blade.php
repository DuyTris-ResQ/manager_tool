<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'License Management Admin')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            emerald: '#10b981',
                            teal: '#14b8a6',
                            mint: '#e8fdf5',
                            dark: '#1a1a1a'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fffe;
        }
        /* Elevate all cards/shadow-sm elements slightly for a more premium floating look */
        .shadow-sm {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
            border-color: rgba(241, 245, 249, 0.5) !important;
        }
        .header-gradient {
            background: linear-gradient(135deg, #e8fdf5 0%, #e0f7fa 40%, #f0f9ff 100%);
        }
        
        /* Desktop Collapsed Sidebar Styles */
        @media (min-width: 768px) {
            aside {
                transition: width 0.2s ease-in-out !important;
            }
            .sidebar-collapsed aside {
                width: 5rem !important; /* w-20 */
            }
            .sidebar-collapsed #sidebar-logo-text {
                display: none !important;
            }
            .sidebar-collapsed aside nav span {
                display: none !important;
            }
            .sidebar-collapsed aside .p-4.border-t .flex.items-center.space-x-3 > div {
                display: none !important;
            }
            .sidebar-collapsed aside nav a {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .sidebar-collapsed aside nav a svg {
                margin-right: 0 !important;
            }
        }

        /* Mobile Hidden/Shown Sidebar Styles */
        @media (max-width: 767px) {
            aside {
                position: fixed !important;
                top: 0;
                left: -100% !important;
                height: 100vh;
                z-index: 40;
                width: 16rem !important; /* w-64 */
                transition: left 0.25s ease-in-out !important;
            }
            .sidebar-open aside {
                left: 0 !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-700 min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-955/40 backdrop-blur-xs z-30 hidden md:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-full md:w-64 md:h-screen md:sticky md:top-0 bg-white border-r border-gray-100 flex flex-col shrink-0 shadow-sm z-40">
        <!-- Logo / Brand -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" id="sidebar-logo" class="flex items-center space-x-2">
                <span class="p-2 bg-gradient-to-br from-cyan-400 to-emerald-400 rounded-xl shadow-md text-white font-bold text-lg">LM</span>
                <span id="sidebar-logo-text" class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-emerald-600 to-cyan-600 bg-clip-text text-transparent">LicenseAdmin</span>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @php
                $route = request()->route()->getName();
            @endphp
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $route === 'admin.dashboard' ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                <span>{{ __('messages.dashboard') }}</span>
            </a>

            <a href="{{ route('admin.licenses.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.licenses') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <span>{{ __('messages.licenses') }}</span>
            </a>

            <a href="{{ route('admin.devices.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.devices') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ __('messages.devices') }}</span>
            </a>

            <a href="{{ route('admin.payments.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.payments') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ __('messages.payments') }}</span>
            </a>

            @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.versions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.versions') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>{{ __('messages.versions') }}</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.users') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>{{ __('messages.users') }}</span>
            </a>
            @endif

            <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.settings') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ __('messages.settings') }}</span>
            </a>

            <a href="{{ route('admin.logs.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_starts_with($route, 'admin.logs') ? 'bg-gradient-to-r from-emerald-500/10 to-teal-500/10 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>{{ __('messages.logs') }}</span>
            </a>
        </nav>

        <!-- Sidebar Footer / Admin profile -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center font-bold text-white shadow-sm text-sm">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 leading-none">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Administrator</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col md:h-screen md:overflow-hidden">
        <!-- Top bar (Header with Mint Gradient & Glassmorphism) -->
        <header class="header-gradient backdrop-blur-md bg-white/70 border-b border-gray-200/50 py-2.5 px-4 md:px-6 flex items-center justify-between z-10 shrink-0">
            <div class="flex items-center space-x-3">
                <!-- Sidebar Toggle Button -->
                <button onclick="toggleSidebar()" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50/50 rounded-xl transition-all active:scale-95 focus:outline-none">
                    <svg id="sidebar-toggle-icon" class="w-6 h-6 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2" />
                        <path d="M9 3v18" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l-3 3 3 3" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-brand-dark tracking-tight leading-tight">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-[11px] text-gray-400 hidden md:block mt-0.5">{{ __('messages.header_subtitle') }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Language Switcher in Admin Panel -->
                <div class="flex items-center space-x-1 text-xs bg-white/80 backdrop-blur-sm border border-gray-200/50 p-1 rounded-xl shadow-sm">
                    <a href="{{ route('lang.change', 'en') }}" class="px-2.5 py-1 rounded-lg transition-all duration-150 {{ App::getLocale() === 'en' ? 'bg-emerald-500 text-white font-bold' : 'text-gray-500 hover:text-gray-700 bg-transparent' }}">EN</a>
                    <a href="{{ route('lang.change', 'vi') }}" class="px-2.5 py-1 rounded-lg transition-all duration-150 {{ App::getLocale() === 'vi' ? 'bg-emerald-500 text-white font-bold' : 'text-gray-500 hover:text-gray-700 bg-transparent' }}">VI</a>
                </div>
            </div>
        </header>

        <!-- Main Body Area -->
        <main class="flex-1 p-4 md:p-8 md:overflow-y-auto">
            <!-- Toast notification messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center space-x-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center space-x-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">
                        {{ session('error') ?: $errors->first() }}
                    </span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar-toggle-icon').classList.toggle('rotate-180');
            if (window.innerWidth >= 768) {
                document.body.classList.toggle('sidebar-collapsed');
            } else {
                document.body.classList.toggle('sidebar-open');
                document.getElementById('sidebar-overlay').classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
