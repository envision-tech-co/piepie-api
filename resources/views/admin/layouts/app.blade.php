<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - PipPip Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81' },
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.2s ease-in-out; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .toggle-switch { position: relative; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; inset: 0; background-color: #cbd5e1; border-radius: 9999px; transition: 0.3s; }
        .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; border-radius: 50%; transition: 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        input:checked + .toggle-slider { background-color: #4f46e5; }
        input:checked + .toggle-slider:before { transform: translateX(20px); }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 antialiased" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="flex min-h-screen">
        {{-- Mobile overlay --}}
        <div x-show="mobileMenu" x-cloak @click="mobileMenu = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition.opacity></div>

        {{-- Sidebar --}}
        <aside
            class="fixed lg:static inset-y-0 left-0 z-50 bg-gradient-to-b from-primary-900 to-primary-800 text-white flex flex-col sidebar-transition shadow-xl"
            :class="[
                sidebarOpen ? 'w-64' : 'w-20',
                mobileMenu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            {{-- Logo --}}
            <div class="flex items-center h-16 px-4 border-b border-white/10">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold">P</span>
                    </div>
                    <span class="text-lg font-bold whitespace-nowrap" x-show="sidebarOpen" x-transition>PipPip Admin</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 space-y-1 overflow-y-auto px-3">
                @php
                    $nav = [
                        ['route' => 'admin.dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
                        ['route' => 'admin.bookings.index', 'icon' => 'calendar', 'label' => 'Bookings'],
                        ['route' => 'admin.bookings.live', 'icon' => 'radio', 'label' => 'Live Bookings'],
                        ['route' => 'admin.customers.index', 'icon' => 'users', 'label' => 'Customers'],
                        ['route' => 'admin.providers.index', 'icon' => 'wrench', 'label' => 'Providers'],
                        ['route' => 'admin.services.index', 'icon' => 'grid', 'label' => 'Service Categories'],
                        ['route' => 'admin.commissions.index', 'icon' => 'percent', 'label' => 'Commissions'],
                    ];
                @endphp

                @foreach ($nav as $n)
                    @php
                        $active = request()->routeIs($n['route']) || request()->routeIs(str_replace('.index', '.*', $n['route']));
                    @endphp
                    <a href="{{ route($n['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                              {{ $active ? 'bg-white/15 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                       title="{{ $n['label'] }}">
                        <span class="flex-shrink-0 w-5 h-5">
                            @include('admin.layouts.icons', ['name' => $n['icon']])
                        </span>
                        <span class="text-sm font-medium whitespace-nowrap" x-show="sidebarOpen" x-transition>{{ $n['label'] }}</span>
                        @if ($n['route'] === 'admin.providers.index' && ($pendingProviders ?? 0) > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 font-bold" x-show="sidebarOpen">
                                {{ $pendingProviders ?? 0 }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Sidebar toggle --}}
            <div class="p-3 border-t border-white/10">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition hidden lg:flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span class="text-xs" x-show="sidebarOpen" x-transition>Collapse</span>
                </button>
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top bar --}}
            <header class="bg-white border-b sticky top-0 z-30 shadow-sm">
                <div class="px-4 lg:px-6 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h2 class="text-lg font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center gap-4" x-data="{ profileOpen: false }">
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                                <div class="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <div class="text-sm font-medium text-gray-800">{{ Auth::guard('admin')->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::guard('admin')->user()->role ?? 'Admin' }}</div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-50">
                                <div class="px-4 py-2 border-b">
                                    <div class="text-sm font-medium text-gray-800">{{ Auth::guard('admin')->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::guard('admin')->user()->email }}</div>
                                </div>
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 lg:p-6 fade-in">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition
                        class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition
                        class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-600 hover:text-red-800">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
