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
<nav class="bg-blue-800 text-white shadow">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <span class="text-xl font-bold tracking-wide">🔵 Optical Inventory</span>
        <div class="flex gap-6 text-sm font-medium">
            <a href="/" class="hover:text-blue-200 transition">Dashboard</a>
            <a href="/stock-in" class="hover:text-blue-200 transition">Stock IN</a>
            <a href="/stock-out" class="hover:text-blue-200 transition">Stock OUT</a>
            <a href="/stock-grid" class="hover:text-blue-200 transition">Stock Grid</a>
            <a href="/transactions" class="hover:text-blue-200 transition">Transactions</a>
            <a href="/reports" class="hover:text-blue-200 transition">Reports</a>
        </div>
    </div>
</nav>

{{-- Page Content --}}
<main class="max-w-7xl mx-auto px-6 py-8">
    @yield('content')
</main>

</body>
</html>