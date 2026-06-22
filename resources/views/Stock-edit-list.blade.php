@extends('layouts.app')
@section('title', 'Edit Stock')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Edit Stock (Manual Correction)</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Search --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/stock-edit" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">🔍 Search by Product, Power, or Category</label>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="e.g. Hoaming, +1.00, hoaming-normal"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="flex-1 sm:flex-none bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm transition">
                Search
            </button>
            @if(request('search'))
            <a href="/stock-edit" class="flex-1 sm:flex-none text-center bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition">
                ✖ Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Desktop Table --}}
<div class="hidden sm:block bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Power</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-center">Current Qty</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $stock->product->name }}</td>
                <td class="px-4 py-3">{{ $stock->power->getLabel() }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $stock->power->category ?? '—' }}</td>
                <td class="px-4 py-3 text-center font-bold">{{ $stock->quantity }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="/stock-edit/{{ $stock->id }}"
                        class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                        ✏️ Edit
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No stock records found</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($stocks->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $stocks->links() }}
    </div>
    @endif
</div>

{{-- Mobile Card View --}}
<div class="sm:hidden space-y-3">
    @forelse($stocks as $stock)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-bold text-gray-800">{{ $stock->product->name }}</p>
                <p class="text-sm text-gray-600">{{ $stock->power->getLabel() }}</p>
                <p class="text-xs text-gray-400">{{ $stock->power->category ?? '—' }}</p>
            </div>
            <span class="text-xl font-bold text-blue-700">{{ $stock->quantity }}</span>
        </div>
        <a href="/stock-edit/{{ $stock->id }}"
            class="block text-center bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-2 rounded-lg text-sm font-medium transition mt-2">
            ✏️ Edit Quantity
        </a>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">No stock records found</div>
    @endforelse

    @if($stocks->hasPages())
    <div class="bg-white rounded-xl shadow p-3">
        {{ $stocks->links() }}
    </div>
    @endif
</div>

@endsection