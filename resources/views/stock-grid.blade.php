@extends('layouts.app')
@section('title', 'Stock Grid')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Stock Grid</h1>

<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/stock-grid" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">🔍 Search Power (e.g. +1.00, hoaming-normal)</label>
            <input type="text" name="search" value="{{ request('search') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm transition">
            Search
        </button>
        @if(request('search'))
        <a href="/stock-grid" class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition text-center">
            ✖ Clear
        </a>
        @endif
    </form>
</div>

{{-- Desktop Grid Table --}}
<div class="hidden sm:block bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Power</th>
                @foreach($products as $product)
                    <th class="px-4 py-3 text-center">{{ $product->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($powers as $power)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-700">{{ $power->getLabel() }}</td>
                @foreach($products as $product)
                    @php $qty = $grid[$power->id][$product->id]; @endphp
                    <td class="px-4 py-3 text-center">
                        @if($qty == 0)
                            <span class="text-gray-300">—</span>
                        @elseif($qty < 5)
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">{{ $qty }}</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">{{ $qty }}</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile Card View --}}
<div class="sm:hidden space-y-3">
    @forelse($powers as $power)
    <div class="bg-white rounded-xl shadow p-4">
        <p class="font-bold text-gray-800 mb-2">{{ $power->getLabel() }}</p>
        <div class="space-y-1">
            @foreach($products as $product)
                @php $qty = $grid[$power->id][$product->id]; @endphp
                <div class="flex justify-between items-center text-sm border-t pt-1.5">
                    <span class="text-gray-600">{{ $product->name }}</span>
                    @if($qty == 0)
                        <span class="text-gray-300">—</span>
                    @elseif($qty < 5)
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">{{ $qty }}</span>
                    @else
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">{{ $qty }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">No powers found</div>
    @endforelse
</div>

@endsection