<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Inventory System') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-sm sm:max-w-md">

        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-700">{{ config('app.name', 'Inventory System') }}</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to continue</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 sm:p-8">

            @if(session('status'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300">
                        Remember me
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                    Sign In
                </button>
            </form>

        </div>

        <p class="text-center text-gray-400 text-xs mt-6">&copy; {{ date('Y') }} {{ config('app.name', 'Inventory System') }}</p>

    </div>

</body>
</html>
