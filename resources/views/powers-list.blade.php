@extends('layouts.app')
@section('title', 'All Powers')

@section('content')

<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">All Powers ({{ $powers->total() }})</h1>
    <a href="/powers/generate"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
        ⚙️ Generate More
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Search / Filter by Category --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/powers" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">🔍 Filter by Category</label>
            <select name="category" onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— All Categories —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('category'))
        <a href="/powers"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition text-center">
            ✖ Clear Filter
        </a>
        @endif
    </form>
</div>

{{-- Category-wise Bulk Delete --}}
@if($categories->count() > 0)
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <h2 class="font-semibold text-gray-700 mb-3 text-sm">Delete Entire Category</h2>
    <div class="flex flex-wrap gap-2">
        @foreach($categories as $cat)
        <form method="POST" action="/powers/category/{{ $cat }}"
            onsubmit="return confirm('Are you sure? This will delete ALL powers under [{{ $cat }}] category!')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                🗑️ {{ $cat }}
            </button>
        </form>
        @endforeach
    </div>
</div>
@endif

{{-- Active Filter Badge --}}
@if(request('category'))
<div class="mb-4">
    <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium">
        Showing: {{ request('category') }} ({{ $powers->total() }} powers)
    </span>
</div>
@endif

{{-- DESKTOP TABLE (hidden on mobile) --}}
<div class="hidden sm:block bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">SPH</th>
                <th class="px-4 py-3 text-left">CYL</th>
                <th class="px-4 py-3 text-left">Label</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($powers as $power)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-xs text-gray-500">{{ $power->category }}</td>
                <td class="px-4 py-3">{{ $power->sph }}</td>
                <td class="px-4 py-3">{{ $power->cyl ?? '—' }}</td>
                <td class="px-4 py-3 font-medium">{{ $power->getLabel() }}</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="/powers/{{ $power->id }}" onsubmit="return confirm('Delete this power?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No powers found for this category</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($powers->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $powers->links() }}
    </div>
    @endif
</div>

{{-- MOBILE CARD VIEW (hidden on desktop) --}}
<div class="sm:hidden space-y-3">
    @forelse($powers as $power)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-bold text-gray-800 text-lg">{{ $power->getLabel() }}</p>
                <p class="text-xs text-gray-500">{{ $power->category }}</p>
            </div>
            <form method="POST" action="/powers/{{ $power->id }}" onsubmit="return confirm('Delete this power?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-xs font-medium">
                    Delete
                </button>
            </form>
        </div>
        <div class="flex gap-4 text-sm text-gray-600 mt-2 pt-2 border-t">
            <span>SPH: <strong>{{ $power->sph }}</strong></span>
            <span>CYL: <strong>{{ $power->cyl ?? '—' }}</strong></span>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
        No powers found for this category
    </div>
    @endforelse

    @if($powers->hasPages())
    <div class="bg-white rounded-xl shadow p-3">
        {{ $powers->links() }}
    </div>
    @endif
</div>

@endsection