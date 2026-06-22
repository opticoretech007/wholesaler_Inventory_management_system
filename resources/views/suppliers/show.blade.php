@extends('layouts.app')
@section('title', 'Supplier Detail')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $supplier->name }}</h1>
    <a href="/suppliers/{{ $supplier->id }}/edit"
        class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-4 py-2 rounded-lg text-sm font-medium">
        ✏️ Edit
    </a>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-blue-700">Rs. {{ number_format($totalPurchases, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Purchases</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($totalPaid, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Paid</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
            Rs. {{ number_format($balance, 2) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">Balance Due</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-gray-700">{{ $purchases->total() }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Purchases</p>
    </div>
</div>

{{-- Supplier Info --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <h2 class="font-semibold text-gray-700 mb-3">Supplier Information</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
        <div><span class="text-gray-400">Phone:</span> <span class="font-medium">{{ $supplier->phone ?? '—' }}</span></div>
        <div><span class="text-gray-400">City:</span> <span class="font-medium">{{ $supplier->city ?? '—' }}</span></div>
        <div><span class="text-gray-400">Status:</span>
            <span class="{{ $supplier->is_active ? 'text-green-600' : 'text-gray-400' }} font-medium">
                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="col-span-2">
            <span class="text-gray-400">Address:</span>
            <span class="font-medium">{{ $supplier->address ?? '—' }}</span>
        </div>
    </div>
</div>

{{-- Purchase History --}}
<div class="bg-white rounded-xl shadow p-4">
    <h2 class="font-semibold text-gray-700 mb-4">Purchase History</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-2">Invoice #</th>
                <th class="pb-2">Date</th>
                <th class="pb-2 text-right">Total</th>
                <th class="pb-2 text-right">Paid</th>
                <th class="pb-2 text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 text-blue-700">{{ $purchase->invoice_no }}</td>
                <td class="py-2 text-gray-500">{{ $purchase->invoice_date }}</td>
                <td class="py-2 text-right">Rs. {{ number_format($purchase->net_total, 2) }}</td>
                <td class="py-2 text-right text-green-600">Rs. {{ number_format($purchase->paid, 2) }}</td>
                <td class="py-2 text-right {{ $purchase->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rs. {{ number_format($purchase->balance, 2) }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-4 text-center text-gray-400">No purchases yet</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($purchases->hasPages())
    <div class="mt-3">{{ $purchases->links() }}</div>
    @endif
</div>

<div class="mt-4">
    <a href="/suppliers" class="text-blue-700 text-sm hover:underline">← Back to Suppliers</a>
</div>

@endsection