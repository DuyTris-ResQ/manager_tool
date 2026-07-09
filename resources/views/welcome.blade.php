<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'OTIO') }}</title>
    @fonts
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Instrument Sans', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', 'Instrument Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-4 lg:p-6 items-center lg:justify-center min-h-screen flex-col">

    <header class="w-full lg:max-w-4xl max-w-[335px] text-xs mb-4 not-has-[nav]:hidden">
        @if (Route::has('login'))
        <nav class="flex items-center justify-between lg:justify-end gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2 lg:hidden">
                <img src="{{ asset('dowload_logo.png') }}" alt="OTIO" class="h-7 w-auto object-contain">
                <span class="font-bold text-sm text-gray-800 dark:text-gray-200">OTIO</span>
            </a>
            <div class="flex items-center gap-3 ml-auto lg:ml-0">
                @auth
                <a href="{{ url('/dashboard') }}" class="inline-block px-4 py-1 dark:text-[#EDEDEC] border border-[#19140035] hover:border-[#1915014a] text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-xs leading-normal">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="inline-block px-4 py-1 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-xs leading-normal">Log in</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-4 py-1 dark:text-[#EDEDEC] border border-[#19140035] hover:border-[#1915014a] text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-xs leading-normal">Register</a>
                @endif
                @endauth
            </div>
        </nav>
        @endif
    </header>

    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">

            <!-- Left Content -->
            <div class="text-xs leading-[18px] flex-1 p-5 pb-6 lg:p-16 lg:pb-8 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                <h1 class="mb-1 font-medium text-sm">Get started with OTIO</h1>
                <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Start with these steps:</p>
                <ul class="flex flex-col mb-3 lg:mb-5">
                    <li class="flex items-center gap-3 py-1 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.3rem] before:absolute">
                        <span class="relative py-0.5 bg-white dark:bg-[#161615]">
                            <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3 h-3 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                            </span>
                        </span>
                        <span class="whitespace-nowrap">
                            Read the
                            <a href="https://otio.com/docs" target="_blank" class="inline-flex items-center gap-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                <span>Documentation</span>
                                <svg width="8" height="9" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2 h-2 shrink-0"><path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/></svg>
                            </a>
                        </span>
                    </li>
                    <li class="flex items-center gap-3 py-1 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:bottom-1/2 before:top-0 before:left-[0.3rem] before:absolute">
                        <span class="relative py-0.5 bg-white dark:bg-[#161615]">
                            <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3 h-3 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                            </span>
                        </span>
                        <span class="whitespace-nowrap">
                            <a href="https://otio.com/tutorials" target="_blank" class="inline-flex items-center gap-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                <span>Tutorials</span>
                                <svg width="8" height="9" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2 h-2 shrink-0"><path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/></svg>
                            </a>
                        </span>
                    </li>
                </ul>
                <ul class="flex gap-2 text-xs leading-normal">
                    <li>
                        <a href="https://cloud.otio.com" target="_blank" class="inline-block dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black px-4 py-1 bg-[#1b1b18] rounded-sm border border-black text-white text-xs leading-normal">Deploy now</a>
                    </li>
                </ul>
                <p class="mt-4 lg:mt-8 text-[#706f6c] dark:text-[#A1A09A]">
                    v{{ app()->version() }}
                    <a href="https://github.com/otio/framework/blob/main/CHANGELOG.md" target="_blank" class="inline-flex items-center gap-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                        <span>Changelog</span>
                        <svg width="8" height="9" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2 h-2"><path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/></svg>
                    </a>
                </p>
            </div>

            <!-- Right: OTIO Logo -->
            <div class="bg-gradient-to-br from-[#e0f7fa] via-[#fff2f2] to-[#f0f9ff] dark:from-[#0a1628] dark:via-[#1D0002] dark:to-[#0a0a0a] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[4/3] lg:aspect-auto w-full lg:w-[480px] shrink-0 overflow-hidden flex items-center justify-center p-6 lg:p-10">
                <img src="{{ asset('dowload_logo.png') }}" alt="OTIO Logo" class="w-full h-full object-contain max-w-[90%] max-h-[90%] drop-shadow-xl transition-all duration-750 starting:opacity-0 motion-safe:starting:translate-y-6 opacity-100 translate-y-0" />
                <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] pointer-events-none"></div>
            </div>

        </main>
    </div>

    @if (Route::has('login'))
    <div class="h-10 lg:h-14.5"></div>
    @endif

</body>
</html>
