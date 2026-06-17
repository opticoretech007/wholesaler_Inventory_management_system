@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-5xl font-bold text-blue-700">{{ $totalStock }}</p>
        <p class="text-gray-500 mt-2 text-sm">Total Stock</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-5xl font-bold {{ $lowStock->count() > 0 ? 'text-red-600' : 'text-green-600' }}">
            {{ $lowStock->count() }}
        </p>
        <p class="text-gray-500 mt-2 text-sm">Low Stock Alerts</p>
    </div>
</div>

{{-- Low Stock Alerts --}}
@if($lowStock->count() > 0)
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-8">
    <h2 class="text-red-700 font-semibold text-lg mb-3">⚠️ Low Stock Alerts</h2>
    <table class="w-full text-sm">
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
</div>
@endif

{{-- Recent Transactions --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-gray-700 font-semibold text-lg mb-4">Recent Transactions</h2>
    <table class="w-full text-sm">
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
</div>

@endsection