@extends('layouts.app')
@section('title', 'Reports')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Reports</h1>

{{-- Export Buttons --}}
<div class="flex gap-4 mb-8">
    <a href="/reports/pdf/stock"
        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
        📄 Export Stock Report PDF
    </a>
    <a href="/reports/pdf/transactions"
        class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
        📄 Export Transactions PDF
    </a>
</div>

{{-- Stock Summary --}}
<div class="bg-white rounded-xl shadow p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-700">Current Stock Summary</h2>
        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm font-bold">
            Total: {{ $totalStock }} units
        </span>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-2">Product</th>
                <th class="pb-2">Power</th>
                <th class="pb-2 text-center">Quantity</th>
                <th class="pb-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockSummary as $stock)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2">{{ $stock->product->name }}</td>
                <td class="py-2">{{ $stock->power->getLabel() }}</td>
                <td class="py-2 text-center font-medium">{{ $stock->quantity }}</td>
                <td class="py-2 text-center">
                    @if($stock->quantity == 0)
                        <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs">Out of Stock</span>
                    @elseif($stock->quantity < 5)
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Low Stock</span>
                    @else
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">In Stock</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-4 text-center text-gray-400">No stock data found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Low Stock --}}
@if($lowStock->count() > 0)
<div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
    <h2 class="text-lg font-semibold text-red-700 mb-4">⚠️ Low Stock Items</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-red-600 border-b border-red-200">
                <th class="pb-2">Product</th>
                <th class="pb-2">Power</th>
                <th class="pb-2 text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStock as $item)
            <tr class="border-b border-red-100">
                <td class="py-2">{{ $item->product->name }}</td>
                <td class="py-2">{{ $item->power->getLabel() }}</td>
                <td class="py-2 text-center text-red-600 font-bold">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Recent Transactions --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Transactions</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-2">Date</th>
                <th class="pb-2">Product</th>
                <th class="pb-2">Power</th>
                <th class="pb-2 text-center">Type</th>
                <th class="pb-2 text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $tx)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 text-gray-500">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                <td class="py-2">{{ $tx->product->name }}</td>
                <td class="py-2">{{ $tx->power->getLabel() }}</td>
                <td class="py-2 text-center">
                    @if($tx->type == 'IN')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">IN</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">OUT</span>
                    @endif
                </td>
                <td class="py-2 text-center font-medium">{{ $tx->quantity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-4 text-center text-gray-400">No transactions yet</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection