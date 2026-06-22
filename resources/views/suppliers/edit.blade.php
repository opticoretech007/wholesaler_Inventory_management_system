@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Edit Supplier</h1>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/suppliers/{{ $supplier->id }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city', $supplier->city) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance (Rs.)</label>
                <input type="number" name="opening_balance"
                    value="{{ old('opening_balance', $supplier->opening_balance) }}" step="0.01"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1"
                    {{ $supplier->is_active ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Active</span>
            </label>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" rows="2"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('address', $supplier->address) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes', $supplier->notes) }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded-lg transition text-sm">
                💾 Update Supplier
            </button>
            <a href="/suppliers"
                class="flex-1 text-center bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold py-2 rounded-lg transition text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection