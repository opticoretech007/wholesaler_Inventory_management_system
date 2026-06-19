@extends('layouts.app')
@section('title', 'All Powers')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">All Powers ({{ $powers->total() }})</h1>
    <a href="/powers/generate"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        ⚙️ Generate More
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Search / Filter by Category --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/powers" class="flex gap-3 items-end flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">🔍 Filter by Category</label>
            <select name="category" onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— All Categories —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('category'))
        <a href="/powers"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition">
            ✖ Clear Filter
        </a>
        @endif
    </form>
</div>

{{-- Category-wise Bulk Delete --}}
@if($categories->count() > 0)
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <h2 class="font-semibold text-gray-700 mb-3 text-sm">Delete Entire Category</h2>
    <div class="flex flex-wrap gap-3">
        @foreach($categories as $cat)
        <form method="POST" action="/powers/category/{{ $cat }}"
            onsubmit="return confirm('Are you sure? This will delete ALL powers under [{{ $cat }}] category!')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                🗑️ Delete "{{ $cat }}"
            </button>
        </form>
        @endforeach
    </div>
</div>
@endif

{{-- Active Filter Badge --}}
@if(request('category'))
<div class="mb-4">
    <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-sm font-medium">
        Showing: {{ request('category') }} ({{ $powers->total() }} powers)
    </span>
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">SPH</th>
                <th class="px-4 py-3 text-left">CYL</th>
                <th class="px-4 py-3 text-left">Label</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($powers as $power)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-xs text-gray-500">{{ $power->category }}</td>
                <td class="px-4 py-3">{{ $power->sph }}</td>
                <td class="px-4 py-3">{{ $power->cyl ?? '—' }}</td>
                <td class="px-4 py-3 font-medium">{{ $power->getLabel() }}</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="/powers/{{ $power->id }}" onsubmit="return confirm('Delete this power?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No powers found for this category</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($powers->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $powers->links() }}
    </div>
    @endif
</div>

@endsection