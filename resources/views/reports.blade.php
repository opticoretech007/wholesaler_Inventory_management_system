@extends('layouts.app')
@section('title', 'Reports')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Reports</h1>

{{-- Export Buttons --}}
<div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-8">
    <a href="/reports/pdf/stock"
        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition text-center">
        📄 Export Stock Report PDF
    </a>
    <a href="/reports/pdf/transactions"
        class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition text-center">
        📄 Export Transactions PDF
    </a>
</div>

{{-- Stock Summary --}}
<div class="bg-white rounded-xl shadow p-4 sm:p-6 mb-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
        <h2 class="text-lg font-semibold text-gray-700">Current Stock Summary</h2>
        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm font-bold inline-block w-fit">
            Total: {{ $totalStock }} units
        </span>
    </div>

    {{-- Desktop Table --}}
    <table class="w-full text-sm hidden sm:table">
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
            <tr><td colspan="4" class="py-4 text-center text-gray-400">No stock data found</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Mobile Cards --}}
    <div class="sm:hidden space-y-3">
        @forelse($stockSummary as $stock)
        <div class="border-b pb-3">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium text-sm">{{ $stock->product->name }}</p>
                    <p class="text-xs text-gray-500">{{ $stock->power->getLabel() }}</p>
                </div>
                <span class="font-bold">{{ $stock->quantity }}</span>
            </div>
            <div class="mt-1">
                @if($stock->quantity == 0)
                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs">Out of Stock</span>
                @elseif($stock->quantity < 5)
                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Low Stock</span>
                @else
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">In Stock</span>
                @endif
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 py-4">No stock data found</p>
        @endforelse
    </div>
</div>

{{-- Low Stock --}}
@if($lowStock->count() > 0)
<div class="bg-red-50 border border-red-200 rounded-xl p-4 sm:p-6 mb-8">
    <h2 class="text-lg font-semibold text-red-700 mb-4">⚠️ Low Stock Items</h2>

    <table class="w-full text-sm hidden sm:table">
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

    <div class="sm:hidden space-y-2">
        @foreach($lowStock as $item)
        <div class="bg-white rounded-lg p-3 flex justify-between items-center">
            <div>
                <p class="font-medium text-sm">{{ $item->product->name }}</p>
                <p class="text-xs text-gray-500">{{ $item->power->getLabel() }}</p>
            </div>
            <span class="text-red-600 font-bold">{{ $item->quantity }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Transactions --}}
<div class="bg-white rounded-xl shadow p-4 sm:p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Transactions</h2>

    <table class="w-full text-sm hidden sm:table">
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
            <tr><td colspan="5" class="py-4 text-center text-gray-400">No transactions yet</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="sm:hidden space-y-3">
        @forelse($recentTransactions as $tx)
        <div class="border-b pb-3">
            <div class="flex justify-between items-center mb-1">
                <span class="font-medium text-sm">{{ $tx->product->name }}</span>
                @if($tx->type == 'IN')
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">IN</span>
                @else
                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">OUT</span>
                @endif
            </div>
            <div class="flex justify-between text-xs text-gray-500">
                <span>{{ $tx->power->getLabel() }} • {{ $tx->created_at->format('d M, h:i A') }}</span>
                <span class="font-bold text-gray-700">Qty: {{ $tx->quantity }}</span>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 py-4">No transactions yet</p>
        @endforelse
    </div>
</div>

@endsection