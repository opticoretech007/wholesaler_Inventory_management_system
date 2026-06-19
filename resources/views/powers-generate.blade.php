@extends('layouts.app')
@section('title', 'Generate Powers')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Generate Lens Powers</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-700">
    💡 <strong>Tip:</strong> Har category (Normal / Cross / High) ke liye ye form alag se submit karo.
    Example: Hoaming Normal ke liye SPH 0.00 to -6.00, CYL 0.00 to -4.00 daal kar generate karo.
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/powers/generate">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Category Label</label>
            <input type="text" name="category" required placeholder="e.g. hoaming-normal, hoaming-cross, hoaming-high"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Is se baad mein filter karna easy hoga (e.g. fromeyes-high)</p>
        </div>

        <h2 class="font-semibold text-gray-700 mb-3">SPH Range</h2>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Start</label>
                <input type="number" step="0.25" name="sph_start" required placeholder="e.g. 0.00 or -6.00"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">End</label>
                <input type="number" step="0.25" name="sph_end" required placeholder="e.g. -6.00 or +6.00"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Step</label>
                <input type="number" step="0.01" name="sph_step" value="0.25" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <h2 class="font-semibold text-gray-700 mb-3">CYL Range</h2>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Start</label>
                <input type="number" step="0.25" name="cyl_start" required placeholder="e.g. 0.00"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">End</label>
                <input type="number" step="0.25" name="cyl_end" required placeholder="e.g. -4.00"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Step</label>
                <input type="number" step="0.01" name="cyl_step" value="0.25" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6 text-sm text-yellow-700">
            ⚠️ Total combinations = SPH values × CYL values. Bohot bara range mat banao ek sath (max ~500 recommended).
        </div>

        <button type="submit"
            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded-lg transition text-sm">
            🚀 Generate Powers
        </button>
    </form>
</div>

<a href="/powers" class="inline-block mt-4 text-blue-700 text-sm hover:underline">
    → View All Powers
</a>

@endsection