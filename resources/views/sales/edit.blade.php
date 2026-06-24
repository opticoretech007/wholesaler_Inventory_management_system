@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Sale: {{ $sale->invoice_no }}</h1>

    <form method="POST" action="{{ route('sales.update', $sale->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Customer</label>
            <select name="customer_id" class="block w-full border rounded p-2">
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $sale->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Invoice Date</label>
            <input type="date" name="invoice_date" value="{{ $sale->invoice_date }}" class="border p-2" />
        </div>

        <div class="mb-3">
            <label>Discount</label>
            <input name="discount" value="{{ $sale->discount }}" class="border p-2" />
        </div>

        <div class="mb-3">
            <label>Tax</label>
            <input name="tax" value="{{ $sale->tax }}" class="border p-2" />
        </div>

        <div class="mb-3">
            <label>Payment Mode</label>
            <select name="payment_mode" class="border p-2">
                <option value="cash" {{ $sale->payment_mode == 'cash' ? 'selected' : ''}}>Cash</option>
                <option value="credit" {{ $sale->payment_mode == 'credit' ? 'selected' : ''}}>Credit</option>
                <option value="bank" {{ $sale->payment_mode == 'bank' ? 'selected' : ''}}>Bank</option>
            </select>
        </div>

        <button class="px-3 py-2 bg-blue-600 text-white rounded">Save</button>
    </form>
</div>

@endsection
