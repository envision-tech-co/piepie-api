<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - PipPip Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('head')
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: true }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="bg-indigo-900 text-white transition-all duration-200 flex flex-col"
            :class="sidebarOpen ? 'w-64' : 'w-20'"
        >
            <div class="flex items-center justify-between h-16 px-4 border-b border-indigo-800">
                <h1 class="text-xl font-bold" x-show="sidebarOpen">PipPip Admin</h1>
                <span class="text-xl font-bold" x-show="!sidebarOpen">PP</span>
                <button @click="sidebarOpen = !sidebarOpen" class="text-indigo-300 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
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
                       class="flex items-center px-4 py-3 hover:bg-indigo-800 transition {{ $active ? 'bg-indigo-800 border-l-4 border-white' : '' }}">
                        <span class="inline-block w-5 text-center">
                            @include('admin.layouts.icons', ['name' => $n['icon']])
                        </span>
                        <span class="ml-3 text-sm" x-show="sidebarOpen">{{ $n['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-indigo-800 text-xs text-indigo-300" x-show="sidebarOpen">
                v1.0 · {{ date('Y') }}
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col">
            {{-- Top bar --}}
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-800">{{ Auth::guard('admin')->user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Auth::guard('admin')->user()->email }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-2 rounded-lg transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
