@extends('layouts.app')
@section('title', 'Total Stock')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Total Stock</h1>

    {{-- View Toggle --}}
    <div class="flex bg-white rounded-lg shadow overflow-hidden">
        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
            class="px-4 py-2 text-sm font-medium {{ $view == 'list' ? 'bg-blue-700 text-white' : 'text-gray-600' }}">
            📋 List View
        </a>
        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
            class="px-4 py-2 text-sm font-medium {{ $view == 'grid' ? 'bg-blue-700 text-white' : 'text-gray-600' }}">
            🔲 Grid View
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl font-bold text-blue-700">{{ $totalQuantity }}</p>
        <p class="text-gray-500 mt-2 text-sm">Total Units in Stock</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl font-bold text-green-700">{{ $totalItems }}</p>
        <p class="text-gray-500 mt-2 text-sm">Different Power Items</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/total-stock" class="flex gap-4 items-end flex-wrap">
        <input type="hidden" name="view" value="{{ $view }}">

        <div>
            <label class="block text-xs text-gray-500 mb-1">Product</label>
            <select name="product_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Category</label>
            <select name="category" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('product_id') || request('category'))
        <a href="/total-stock?view={{ $view }}"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition">
            ✖ Clear Filters
        </a>
        @endif
    </form>
</div>

{{-- LIST VIEW --}}
@if($view == 'list')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Power</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks->sortBy('product.name') as $stock)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $stock->product->name }}</td>
                <td class="px-4 py-3">{{ $stock->power->getLabel() }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $stock->power->category ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    @if($stock->quantity < 5)
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">
                            {{ $stock->quantity }}
                        </span>
                    @else
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                            {{ $stock->quantity }}
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-400">No stock found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- GRID VIEW --}}
@if($view == 'grid')
<div class="bg-white rounded-xl shadow overflow-x-auto">
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
            @forelse($powersWithStock as $power)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-700">
                    {{ $power->getLabel() }}
                    <span class="text-xs text-gray-400 block">{{ $power->category }}</span>
                </td>
                @foreach($products as $product)
                    @php $qty = $grid[$power->id][$product->id] ?? 0; @endphp
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
            @empty
            <tr>
                <td colspan="{{ $products->count() + 1 }}" class="px-4 py-8 text-center text-gray-400">No stock found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection