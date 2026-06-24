@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold">Invoice: {{ $sale->invoice_no }}</h1>
    <p>Customer: {{ $sale->customer->name }}</p>
    <p>Date: {{ $sale->invoice_date }}</p>

    <table class="w-full mt-4 table-auto border-collapse">
        <thead>
            <tr>
                <th class="border px-2">Product</th>
                <th class="border px-2">Power</th>
                <th class="border px-2">Qty</th>
                <th class="border px-2">Unit</th>
                <th class="border px-2">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $it)
                <tr>
                    <td class="border px-2">{{ $it->product->name ?? $it->product_id }}</td>
                    <td class="border px-2">{{ $it->power->getlabel() ?? $it->power_id }}</td>
                    <td class="border px-2">{{ $it->quantity }}</td>
                    <td class="border px-2">{{ number_format($it->unit_price,2) }}</td>
                    <td class="border px-2">{{ number_format($it->total_price,2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 text-right">
        <p>Gross: Rs. {{ number_format($sale->gross_total,2) }}</p>
        <p>Discount: Rs. {{ number_format($sale->discount,2) }}</p>
        <p>Tax: Rs. {{ number_format($sale->tax,2) }}</p>
        <p class="font-bold">Net: Rs. {{ number_format($sale->net_total,2) }}</p>
        <p>Paid: Rs. {{ number_format($sale->paid,2) }}</p>
        <p>Balance: Rs. {{ number_format($sale->balance,2) }}</p>
        <div class="mt-3 text-left">
            <h3 class="font-semibold">Payment Info</h3>
            <div><span class="text-gray-400">Mode:</span> <span class="font-medium capitalize">{{ $sale->payment_mode }}</span></div>
            @if($sale->payments->count())
                <ul class="mt-2">
                @foreach($sale->payments as $p)
                    <li>Rs. {{ number_format($p->amount,2) }} — {{ ucfirst($p->method) }} ({{ $p->date ?? $p->created_at->toDateString() }})</li>
                @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>

@endsection
