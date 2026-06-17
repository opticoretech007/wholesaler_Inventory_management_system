@extends('layouts.app')
@section('title', 'Transactions')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Transaction History</h1>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/transactions" class="flex gap-4 flex-wrap items-end">

        <div>
            <label class="block text-xs text-gray-500 mb-1">Product</label>
            <select name="product_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Type</label>
            <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>IN</option>
                <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>OUT</option>
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <button type="submit"
            class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800 transition">
            Filter
        </button>

        <a href="/transactions"
            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
            Reset
        </a>

    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Power</th>
                <th class="px-4 py-3 text-center">Type</th>
                <th class="px-4 py-3 text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400">{{ $tx->id }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                <td class="px-4 py-3 font-medium">{{ $tx->product->name }}</td>
                <td class="px-4 py-3">{{ $tx->power->getLabel() }}</td>
                <td class="px-4 py-3 text-center">
                    @if($tx->type == 'IN')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">IN</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">OUT</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center font-medium">{{ $tx->quantity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No transactions found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($transactions->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif

</div>

@endsection