<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optical Inventory — @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navbar --}}
<nav class="bg-blue-800 text-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between">
        <span class="text-lg sm:text-xl font-bold tracking-wide">🔵 Optical Inventory</span>

        {{-- Desktop Links --}}
        <div class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="/" class="hover:text-blue-200 transition">Dashboard</a>
            <a href="/stock-in" class="hover:text-blue-200 transition">Stock IN</a>
            <a href="/stock-out" class="hover:text-blue-200 transition">Stock OUT</a>
            <a href="/stock-grid" class="hover:text-blue-200 transition">Stock Grid</a>
            <a href="/transactions" class="hover:text-blue-200 transition">Transactions</a>
            <a href="/reports" class="hover:text-blue-200 transition">Reports</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-blue-900 hover:bg-blue-950 px-3 py-1.5 rounded-lg text-sm font-medium transition">
                    Logout
                </button>
            </form>
        </div>

        {{-- Mobile Hamburger Button --}}
        <button id="navToggleBtn" onclick="toggleMobileNav()" class="md:hidden p-2 -mr-2 rounded-lg hover:bg-blue-700 transition" aria-label="Toggle menu">
            <svg id="navIconOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg id="navIconClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Mobile Drawer --}}
    <div id="mobileNav" class="hidden md:hidden bg-blue-900 px-4 pb-4">
        <div class="flex flex-col gap-1 pt-2 text-sm font-medium">
            <a href="/" class="px-3 py-2.5 rounded-lg hover:bg-blue-800 transition">📊 Dashboard</a>
            <a href="/stock-in" class="px-3 py-2.5 rounded-lg hover:bg-blue-800 transition">➕ Stock IN</a>
            <a href="/stock-out" class="px-3 py-2.5 rounded-lg hover:bg-blue-800 transition">➖ Stock OUT</a>
            <a href="/stock-grid" class="px-3 py-2.5 rounded-lg hover:bg-blue-800 transition">🔢 Stock Grid</a>
            <a href="/transactions" class="px-3 py-2.5 rounded-lg hover:bg-blue-800 transition">📋 Transactions</a>
            <a href="/reports" class="px-3 py-2.5 rounded-lg hover:bg-blue-800 transition">📄 Reports</a>

            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg bg-blue-950 hover:bg-black transition">
                    🚪 Logout
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Page Content --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
    @yield('content')
</main>

<script>
    function toggleMobileNav() {
        const menu = document.getElementById('mobileNav');
        const iconOpen = document.getElementById('navIconOpen');
        const iconClose = document.getElementById('navIconClose');

        menu.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
    }
</script>

</body>
</html>
