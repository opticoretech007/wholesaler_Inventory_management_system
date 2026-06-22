@extends('layouts.app')
@section('title', 'Purchases')

@section('content')

<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Purchase History</h1>
    <a href="/purchases/create"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
        + New Purchase
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
@endif

{{-- Summary Card --}}
<div class="bg-white rounded-xl shadow p-4 mb-6 text-center">
    <p class="text-3xl font-bold text-blue-700">Rs. {{ number_format($totalPurchases, 2) }}</p>
    <p class="text-xs text-gray-500 mt-1">Total Purchases</p>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/purchases" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">🔍 Search Invoice # or Supplier</label>
            <input type="text" name="search" value="{{ request('search') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-700 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-800">
                Filter
            </button>
            @if(request('search') || request('date'))
            <a href="/purchases" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
                ✖ Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Desktop Table --}}
<div class="hidden sm:block bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Invoice #</th>
                <th class="px-4 py-3 text-left">Supplier</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-right">Paid</th>
                <th class="px-4 py-3 text-right">Balance</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-blue-700">
                    <a href="/purchases/{{ $purchase->id }}">{{ $purchase->invoice_no }}</a>
                </td>
                <td class="px-4 py-3">{{ $purchase->supplier->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $purchase->invoice_date }}</td>
                <td class="px-4 py-3 text-right">Rs. {{ number_format($purchase->net_total, 2) }}</td>
                <td class="px-4 py-3 text-right text-green-600">Rs. {{ number_format($purchase->paid, 2) }}</td>
                <td class="px-4 py-3 text-right {{ $purchase->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rs. {{ number_format($purchase->balance, 2) }}
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/purchases/{{ $purchase->id }}"
                            class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded text-xs">
                            View
                        </a>
                        <form method="POST" action="/purchases/{{ $purchase->id }}"
                            onsubmit="return confirm('Delete this purchase? Stock will be reversed!')">
                            @csrf @method('DELETE')
                            <button class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No purchases found</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($purchases->hasPages())
    <div class="px-4 py-3 border-t">{{ $purchases->links() }}</div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden space-y-3">
    @forelse($purchases as $purchase)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between mb-1">
            <a href="/purchases/{{ $purchase->id }}" class="font-bold text-blue-700">{{ $purchase->invoice_no }}</a>
            <span class="text-xs text-gray-500">{{ $purchase->invoice_date }}</span>
        </div>
        <p class="text-sm text-gray-600 mb-2">{{ $purchase->supplier->name }}</p>
        <div class="flex justify-between text-sm border-t pt-2">
            <span>Total: <strong>Rs. {{ number_format($purchase->net_total, 2) }}</strong></span>
            <span class="{{ $purchase->balance > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                Bal: Rs. {{ number_format($purchase->balance, 2) }}
            </span>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl p-8 text-center text-gray-400">No purchases found</div>
    @endforelse
</div>

@endsection