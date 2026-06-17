@extends('layouts.app')
@section('title', 'Stock IN')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Stock IN</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <form method="POST" action="/stock-in">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Power</label>
            <select name="power_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($powers as $power)
                    <option value="{{ $power->id }}">{{ $power->getLabel() }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input type="number" name="quantity" min="1" placeholder="Enter quantity"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @error('quantity')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded-lg transition text-sm">
            ➕ Add Stock
        </button>
    </form>
</div>

@endsection