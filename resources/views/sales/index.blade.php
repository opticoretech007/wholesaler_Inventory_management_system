@extends('layouts.app')
@section('title', 'Sales')

@section('content')

<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Sales History</h1>
    <a href="{{ url('/sales/create') }}"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
        + New Sale
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('error') }}</div>
@endif

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/sales" class="flex flex-col sm:flex-row gap-3 sm:items-end flex-wrap">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1">🔍 Search Invoice # or Customer</label>
            <input type="text" name="q" value="{{ request('q') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">From Date</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To Date</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-700 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-800">
                Filter
            </button>
            @if(request('q') || request('date_from') || request('date_to'))
            <a href="/sales" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
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
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-right">Net Total</th>
                <th class="px-4 py-3 text-right">Paid</th>
                <th class="px-4 py-3 text-right">Balance</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $s)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-blue-700">
                    <a href="{{ route('sales.show', $s->id) }}">{{ $s->invoice_no }}</a>
                </td>
                <td class="px-4 py-3">{{ $s->customer->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $s->invoice_date }}</td>
                <td class="px-4 py-3 text-right">Rs. {{ number_format($s->net_total, 2) }}</td>
                <td class="px-4 py-3 text-right text-green-600">Rs. {{ number_format($s->paid, 2) }}</td>
                <td class="px-4 py-3 text-right {{ $s->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rs. {{ number_format($s->balance, 2) }}
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('sales.show', $s->id) }}"
                            class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded text-xs">
                            View
                        </a>
                        <a href="{{ route('sales.edit', $s->id) }}"
                            class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded text-xs">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('sales.destroy', $s->id) }}"
                            onsubmit="return confirm('Delete this sale?')">
                            @csrf @method('DELETE')
                            <button class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No sales found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($sales->hasPages())
    <div class="px-4 py-3 border-t">{{ $sales->links() }}</div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden space-y-3">
    @forelse($sales as $s)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between mb-1">
            <a href="{{ route('sales.show', $s->id) }}" class="font-bold text-blue-700">{{ $s->invoice_no }}</a>
            <span class="text-xs text-gray-500">{{ $s->invoice_date }}</span>
        </div>
        <p class="text-sm text-gray-600 mb-2">{{ $s->customer->name ?? '—' }}</p>
        <div class="flex justify-between text-sm border-t pt-2">
            <span>Total: <strong>Rs. {{ number_format($s->net_total, 2) }}</strong></span>
            <span class="{{ $s->balance > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                Bal: Rs. {{ number_format($s->balance, 2) }}
            </span>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl p-8 text-center text-gray-400">No sales found</div>
    @endforelse
</div>

@endsection