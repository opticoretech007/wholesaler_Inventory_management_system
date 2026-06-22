@extends('layouts.app')
@section('title', 'Customers')

@section('content')

<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Customers</h1>
    <a href="/customers/create"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
        + Add Customer
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
@endif

{{-- Search --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/customers" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">🔍 Search by Name, Phone or City</label>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="e.g. Ali Optics, 0300..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-700 text-white px-5 py-2 rounded-lg text-sm transition hover:bg-blue-800">
                Search
            </button>
            @if(request('search'))
            <a href="/customers" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition hover:bg-gray-300">
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
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">City</th>
                <th class="px-4 py-3 text-right">Balance</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400">{{ $customer->id }}</td>
                <td class="px-4 py-3 font-medium">
                    <a href="/customers/{{ $customer->id }}" class="text-blue-700 hover:underline">
                        {{ $customer->name }}
                    </a>
                </td>
                <td class="px-4 py-3">{{ $customer->phone ?? '—' }}</td>
                <td class="px-4 py-3">{{ $customer->city ?? '—' }}</td>
                <td class="px-4 py-3 text-right font-medium
                    {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rs. {{ number_format($customer->current_balance, 2) }}
                </td>
                <td class="px-4 py-3 text-center">
                    @if($customer->is_active)
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Active</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/customers/{{ $customer->id }}/edit"
                            class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded text-xs font-medium">
                            Edit
                        </a>
                        <form method="POST" action="/customers/{{ $customer->id }}"
                            onsubmit="return confirm('Delete this customer?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No customers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($customers->hasPages())
    <div class="px-4 py-3 border-t">{{ $customers->links() }}</div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden space-y-3">
    @forelse($customers as $customer)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <a href="/customers/{{ $customer->id }}" class="font-bold text-blue-700">{{ $customer->name }}</a>
                <p class="text-xs text-gray-500">{{ $customer->phone ?? '—' }} • {{ $customer->city ?? '—' }}</p>
            </div>
            <span class="font-bold text-sm {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rs. {{ number_format($customer->current_balance, 2) }}
            </span>
        </div>
        <div class="flex gap-2 mt-2 border-t pt-2">
            <a href="/customers/{{ $customer->id }}/edit"
                class="flex-1 text-center bg-yellow-100 text-yellow-700 py-1.5 rounded text-xs font-medium">Edit</a>
            <form method="POST" action="/customers/{{ $customer->id }}" class="flex-1"
                onsubmit="return confirm('Delete this customer?')">
                @csrf @method('DELETE')
                <button class="w-full bg-red-100 text-red-700 py-1.5 rounded text-xs font-medium">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl p-8 text-center text-gray-400">No customers found</div>
    @endforelse
</div>

@endsection