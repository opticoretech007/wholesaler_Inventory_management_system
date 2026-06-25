@extends('layouts.app')
@section('title', 'Stock IN')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Stock IN</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="/stock-in">
        @csrf

        {{-- Product --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Category → Class → Subclass → Power --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="si_category" onchange="siLoadClasses(this.value)"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <select id="si_class" onchange="siLoadSubclasses(this.value)"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subclass</label>
                <select id="si_subclass" onchange="siLoadPowers(this.value)"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Power</label>
                <select name="power_id" id="si_power"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                </select>
            </div>
        </div>

        {{-- Quantity --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input type="number" name="quantity" min="1" placeholder="Enter quantity"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- Prices --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-gray-700 mb-3 text-sm">Prices (Rs.)</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">LP Price</label>
                    <input type="number" name="lp_price" step="0.01" value="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Retail Price</label>
                    <input type="number" name="retail_price" step="0.01" value="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Wholesale Price</label>
                    <input type="number" name="wholesale_price" step="0.01" value="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Company Price</label>
                    <input type="number" name="company_price" step="0.01" value="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 rounded-lg transition text-sm">
            ➕ Add Stock
        </button>
    </form>
</div>

<script>
function siLoadClasses(categoryId) {
    const classSelect = document.getElementById('si_class');
    const subSelect   = document.getElementById('si_subclass');
    const powerSelect = document.getElementById('si_power');

    classSelect.innerHTML = '<option value="">-- Select --</option>';
    subSelect.innerHTML   = '<option value="">-- Select --</option>';
    powerSelect.innerHTML = '<option value="">-- Select --</option>';

    if (!categoryId) return;

    fetch(`/api/classes/${categoryId}`)
        .then(r => r.json())
        .then(classes => {
            classes.forEach(c => {
                classSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
}

function siLoadSubclasses(classId) {
    const subSelect   = document.getElementById('si_subclass');
    const powerSelect = document.getElementById('si_power');

    subSelect.innerHTML   = '<option value="">-- Select --</option>';
    powerSelect.innerHTML = '<option value="">-- Select --</option>';

    if (!classId) return;

    fetch(`/api/subclasses/${classId}`)
        .then(r => r.json())
        .then(subclasses => {
            subclasses.forEach(s => {
                subSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
        });
}

function siLoadPowers(subclassId) {
    const powerSelect = document.getElementById('si_power');
    powerSelect.innerHTML = '<option value="">-- Select --</option>';

    if (!subclassId) return;

    fetch(`/api/powers/${subclassId}`)
        .then(r => r.json())
        .then(powers => {
            powers.forEach(p => {
                powerSelect.innerHTML += `<option value="${p.id}">${p.label}</option>`;
            });
        });
}
</script>

@endsection