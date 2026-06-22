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
    <nav class="bg-blue-800 text-white shadow relative">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <span class="text-xl font-bold tracking-wide whitespace-nowrap">Optical Inventory</span>

            {{-- Desktop Menu --}}
            <div class="hidden lg:flex items-center gap-5 text-sm font-medium">
                <a href="/" class="hover:text-blue-200 transition">Dashboard</a>
                <a href="/stock-in" class="hover:text-blue-200 transition">Stock IN</a>
                <a href="/stock-out" class="hover:text-blue-200 transition">Stock OUT</a>
                <a href="/stock-grid" class="hover:text-blue-200 transition">Stock Grid</a>
                <a href="/total-stock" class="hover:text-blue-200 transition">Total Stock</a>
                <a href="/customers" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">Customers</a>
                <a href="/suppliers" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">Suppliers</a>
                <a href="/purchases" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">Purchases</a>
                <a href="/transactions" class="hover:text-blue-200 transition">Transactions</a>

                {{-- Dropdown for Manage --}}
                <div class="relative" x-data="{ open: false }">
                    <button onclick="document.getElementById('manageDropdown').classList.toggle('hidden')"
                        class="hover:text-blue-200 transition flex items-center gap-1">
                        Manage
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="manageDropdown"
                        class="hidden absolute right-0 mt-3 w-44 bg-white text-gray-700 rounded-lg shadow-lg overflow-hidden z-50">
                        <a href="/powers" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">⚙️ Powers</a>
                        <a href="/stock-edit" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">✏️ Edit
                            Stock</a>
                        <a href="/reports" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">📄 Reports</a>
                    </div>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit"
                        class="bg-blue-700 hover:bg-blue-600 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                        Logout
                    </button>
                </form>
            </div>

            {{-- Mobile Hamburger --}}
            <button onclick="document.getElementById('mobileMenu').classList.toggle('hidden')"
                class="lg:hidden text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="hidden lg:hidden bg-blue-900 px-6 py-4 space-y-1 text-sm">
            <a href="/" class="block py-2 hover:text-blue-200 transition">Dashboard</a>
            <a href="/stock-in" class="block py-2 hover:text-blue-200 transition">Stock IN</a>
            <a href="/stock-out" class="block py-2 hover:text-blue-200 transition">Stock OUT</a>
            <a href="/stock-grid" class="block py-2 hover:text-blue-200 transition">Stock Grid</a>
            <a href="/total-stock" class="block py-2 hover:text-blue-200 transition">Total Stock</a>
            <a href="/transactions" class="block py-2 hover:text-blue-200 transition">Transactions</a>
            <a href="/customers" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">Customers</a>
            <a href="/suppliers" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">Suppliers</a>
            <a href="/purchases" class="block px-4 py-2.5 text-sm hover:bg-gray-100 transition">Purchases</a>
            <a href="/powers" class="block py-2 hover:text-blue-200 transition">Powers</a>
            <a href="/stock-edit" class="block py-2 hover:text-blue-200 transition">Edit Stock</a>
            <a href="/reports" class="block py-2 hover:text-blue-200 transition">Reports</a>
            <form method="POST" action="/logout" class="pt-2">
                @csrf
                <button type="submit"
                    class="bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition w-full text-left">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- Page Content --}}
    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
    </main>

</body>

</html>