@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl sm:text-5xl font-bold text-blue-700">{{ $totalStock }}</p>
        <p class="text-gray-500 mt-2 text-sm">Total Stock</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl sm:text-5xl font-bold {{ $lowStock->count() > 0 ? 'text-red-600' : 'text-green-600' }}">
            {{ $lowStock->count() }}
        </p>
        <p class="text-gray-500 mt-2 text-sm">Low Stock Alerts</p>
    </div>
</div>

{{-- Low Stock Alerts --}}
@if($lowStock->count() > 0)
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-8">
    <h2 class="text-red-700 font-semibold text-lg mb-3">⚠️ Low Stock Alerts</h2>

    {{-- Desktop Table --}}
    <table class="w-full text-sm hidden sm:table">
        <thead>
            <tr class="text-left text-red-600 border-b border-red-200">
                <th class="pb-2">Product</th>
                <th class="pb-2">Power</th>
                <th class="pb-2">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStock as $item)
            <tr class="border-b border-red-100">
                <td class="py-2">{{ $item->product->name }}</td>
                <td class="py-2">{{ $item->power->getLabel() }}</td>
                <td class="py-2 text-red-600 font-bold">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Mobile Cards --}}
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
    <h2 class="text-gray-700 font-semibold text-lg mb-4">Recent Transactions</h2>

    {{-- Desktop Table --}}
    <table class="w-full text-sm hidden sm:table">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-2">Date</th>
                <th class="pb-2">Product</th>
                <th class="pb-2">Power</th>
                <th class="pb-2">Type</th>
                <th class="pb-2">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $tx)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 text-gray-500">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                <td class="py-2">{{ $tx->product->name }}</td>
                <td class="py-2">{{ $tx->power->getLabel() }}</td>
                <td class="py-2">
                    @if($tx->type == 'IN')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">IN</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">OUT</span>
                    @endif
                </td>
                <td class="py-2 font-medium">{{ $tx->quantity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-4 text-center text-gray-400">No transactions yet</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Mobile Cards --}}
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