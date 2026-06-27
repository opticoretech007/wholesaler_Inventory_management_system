@extends('layouts.app')
@section('title', 'Purchase Detail')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $purchase->invoice_no }}</h1>
            <p class="text-sm text-gray-500">{{ $purchase->invoice_date }} • {{ $purchase->supplier->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="/purchases" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
                ← Back
            </a>
            <a href="/purchases/{{ $purchase->id }}/edit"
                class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-4 py-2 rounded-lg text-sm">
                ✏️ Edit
            </a>
            <form method="POST" action="/purchases/{{ $purchase->id }}"
                onsubmit="return confirm('Delete this purchase? Stock will be reversed!')">
                @csrf @method('DELETE')
                <button class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold text-gray-700">Rs. {{ number_format($purchase->gross_total, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Gross Total</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold text-blue-700">Rs. {{ number_format($purchase->net_total, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Net Total</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold text-green-700">Rs. {{ number_format($purchase->paid, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Paid</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold {{ $purchase->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rs. {{ number_format($purchase->balance, 2) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Balance</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-gray-700 mb-4">Items ({{ $purchase->items->count() }})</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-blue-800 text-white">
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Power</th>
                    <th class="px-4 py-2 text-center">Qty</th>
                    <th class="px-4 py-2 text-right">Unit Price</th>
                    <th class="px-4 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $i => $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $item->product->name }}</td>
                        <td class="px-4 py-2">{{ $item->power->getLabel() }}</td>
                        <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-2 text-right font-medium">Rs. {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 bg-gray-50">
                    <td colspan="5" class="px-4 py-2 text-right font-semibold">Net Total:</td>
                    <td class="px-4 py-2 text-right font-bold text-blue-700">
                        Rs. {{ number_format($purchase->net_total, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Payment Info --}}
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="font-semibold text-gray-700 mb-3">Payment Info</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
            <div><span class="text-gray-400">Mode:</span> <span
                    class="font-medium capitalize">{{ $purchase->payment_mode }}</span></div>
            <div><span class="text-gray-400">Discount:</span> <span class="font-medium">Rs.
                    {{ number_format($purchase->discount, 2) }}</span></div>
            <div><span class="text-gray-400">Tax:</span> <span class="font-medium">Rs.
                    {{ number_format($purchase->tax, 2) }}</span></div>
            @if($purchase->memo)
                <div class="col-span-2"><span class="text-gray-400">Memo:</span> <span
                        class="font-medium">{{ $purchase->memo }}</span></div>
            @endif
        </div>
    </div>

@endsection