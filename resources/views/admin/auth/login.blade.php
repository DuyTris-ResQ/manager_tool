<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - License Management Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(180deg, #e8fdf5 0%, #e0f7fa 30%, #f0f9ff 60%, #ffffff 100%);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-700 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white/70 backdrop-blur-lg border border-white/50 shadow-xl rounded-3xl p-8 space-y-6">
        
        <!-- Language Switcher -->
        <div class="flex justify-end space-x-1.5 text-xs">
            <a href="{{ route('lang.change', 'en') }}" class="px-2.5 py-1 rounded-xl border {{ App::getLocale() === 'en' ? 'bg-emerald-500 text-white border-emerald-500 font-bold' : 'text-gray-500 border-gray-200 hover:text-gray-700 bg-white transition-colors' }}">EN</a>
            <a href="{{ route('lang.change', 'vi') }}" class="px-2.5 py-1 rounded-xl border {{ App::getLocale() === 'vi' ? 'bg-emerald-500 text-white border-emerald-500 font-bold' : 'text-gray-500 border-gray-200 hover:text-gray-700 bg-white transition-colors' }}">VI</a>
        </div>

        <!-- Logo -->
        <div class="text-center space-y-2">
            <div class="inline-flex p-3 bg-gradient-to-br from-cyan-400 to-emerald-400 rounded-2xl shadow-md text-white font-extrabold text-2xl">LM</div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ __('messages.welcome_back') }}</h2>
            <p class="text-xs text-gray-500">{{ __('messages.sign_in_desc') }}</p>
        </div>

        <!-- Alerts -->
        @if($errors->any())
            <div class="p-3.5 bg-red-50 border border-red-100 text-red-700 rounded-2xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.email_address') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'admin@gmail.com') }}" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/80 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm" placeholder="name@example.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('messages.password') }}</label>
                <input type="password" id="password" name="password" value="admin" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white/80 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-gray-500 cursor-pointer">
                    <input type="checkbox" name="remember" checked class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500/20">
                    <span>{{ __('messages.remember_me') }}</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 text-sm font-bold rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 text-white hover:from-cyan-600 hover:to-emerald-600 shadow-lg shadow-cyan-200/50 hover:shadow-cyan-300/50 transition-all active:scale-[0.98]">
                {{ __('messages.sign_in') }}
            </button>
        </form>

        <div class="text-center">
            <p class="text-xs text-gray-400">{{ __('messages.default_credentials') }}: <span class="font-semibold text-gray-600">admin@gmail.com</span> / <span class="font-semibold text-gray-600">admin</span></p>
        </div>
    </div>

</body>
</html>
