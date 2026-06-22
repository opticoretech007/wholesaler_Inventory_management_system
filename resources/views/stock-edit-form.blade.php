@extends('layouts.app')
@section('title', 'Edit Stock')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Stock Quantity</h1>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-500">Product</p>
        <p class="font-semibold text-gray-800">{{ $stock->product->name }}</p>

        <p class="text-sm text-gray-500 mt-3">Power</p>
        <p class="font-semibold text-gray-800">{{ $stock->power->getLabel() }}</p>

        <p class="text-sm text-gray-500 mt-3">Current Quantity</p>
        <p class="font-bold text-2xl text-blue-700">{{ $stock->quantity }}</p>
    </div>

    <form method="POST" action="/stock-edit/{{ $stock->id }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">New Quantity</label>
            <input type="number" name="quantity" min="0" value="{{ $stock->quantity }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @error('quantity')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Correction</label>
            <input type="text" name="reason" placeholder="e.g. Physical count mismatch, damaged stock, etc."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @error('reason')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded-lg transition text-sm">
                💾 Update Stock
            </button>
            <a href="/stock-edit"
                class="flex-1 text-center bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold py-2 rounded-lg transition text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection