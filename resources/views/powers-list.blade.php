@extends('layouts.app')
@section('title', 'All Powers')

@section('content')

<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
        All Powers <span class="text-gray-400 text-base font-normal">({{ $totalPowers }} total)</span>
    </h1>
    <a href="/powers/generate"
        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
        ⚙️ Generate More
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
@endif

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="/powers" class="flex flex-col sm:flex-row gap-3 sm:items-end flex-wrap">

        <div>
            <label class="block text-xs text-gray-500 mb-1">Category</label>
            <select name="category_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Class</label>
            <select name="class_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Classes</option>
                @foreach($categories as $cat)
                    @foreach($cat->classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Subclass</label>
            <select name="subclass_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Subclasses</option>
                @foreach($categories as $cat)
                    @foreach($cat->classes as $class)
                        @foreach($class->subclasses as $sub)
                            <option value="{{ $sub->id }}" {{ request('subclass_id') == $sub->id ? 'selected' : '' }}>
                                {{ $cat->name }} → {{ $class->name }} → {{ $sub->name }}
                            </option>
                        @endforeach
                    @endforeach
                @endforeach
            </select>
        </div>

        @if(request('category_id') || request('class_id') || request('subclass_id'))
        <a href="/powers"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm transition">
            ✖ Clear
        </a>
        @endif

    </form>
</div>

{{-- Showing filter badge --}}
@if(request('subclass_id') || request('class_id') || request('category_id'))
<div class="mb-4">
    <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-sm font-medium">
        Showing {{ $powers->total() }} powers
    </span>
</div>
@endif

{{-- Desktop Table --}}
<div class="hidden sm:block bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-blue-800 text-white">
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Subclass</th>
                <th class="px-4 py-3 text-left">SPH</th>
                <th class="px-4 py-3 text-left">CYL</th>
                <th class="px-4 py-3 text-left">Label</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($powers as $power)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-xs text-gray-500">{{ $power->subclass->lensClass->category->name ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $power->subclass->lensClass->name ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-gray-600">{{ $power->subclass->name ?? '—' }}</td>
                <td class="px-4 py-3">{{ $power->sph }}</td>
                <td class="px-4 py-3">{{ $power->cyl ?? '—' }}</td>
                <td class="px-4 py-3 font-medium">{{ $power->getLabel() }}</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="/powers/{{ $power->id }}"
                        onsubmit="return confirm('Delete this power?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No powers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($powers->hasPages())
    <div class="px-4 py-3 border-t">{{ $powers->links() }}</div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden space-y-3">
    @forelse($powers as $power)
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-bold text-gray-800 text-lg">{{ $power->getLabel() }}</p>
                <p class="text-xs text-gray-500">{{ $power->subclass->lensClass->category->name ?? '—' }}</p>
                <p class="text-xs text-gray-500">{{ $power->subclass->lensClass->name ?? '—' }} → {{ $power->subclass->name ?? '—' }}</p>
            </div>
            <form method="POST" action="/powers/{{ $power->id }}"
                onsubmit="return confirm('Delete this power?')">
                @csrf @method('DELETE')
                <button class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-xs font-medium">Delete</button>
            </form>
        </div>
        <div class="flex gap-4 text-sm text-gray-600 mt-2 pt-2 border-t">
            <span>SPH: <strong>{{ $power->sph }}</strong></span>
            <span>CYL: <strong>{{ $power->cyl ?? '—' }}</strong></span>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">No powers found</div>
    @endforelse
</div>

@endsection