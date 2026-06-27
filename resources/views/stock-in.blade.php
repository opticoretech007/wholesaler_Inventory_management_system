@extends('layouts.app')
@section('title', 'Stock IN')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Stock IN</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">✅ {{ session('success') }}</div>
@endif

<form method="POST" action="/stock-in" id="stockInForm">
@csrf

{{-- Step 1: Product + Category + Class + Subclass --}}
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 text-sm">Step 1 — Select Product & Category</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select id="si_category" onchange="siLoadClasses(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
            <select id="si_class" onchange="siLoadSubclasses(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Class --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subclass</label>
            <select id="si_subclass" onchange="siLoadPowers(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Subclass --</option>
            </select>
        </div>
    </div>
</div>

{{-- Step 2: Prices --}}
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4 text-sm">Step 2 — Set Prices & Quantity</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Quantity Each</label>
            <input type="number" name="quantity" min="1" value="1"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
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
        
    </div>
</div>

{{-- Step 3: Powers Checkboxes --}}
<div class="bg-white rounded-xl shadow mb-4" id="powersSection" style="display:none">
    <div class="p-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-700 text-sm">
            Step 3 — Select Powers
            <span class="text-gray-400 font-normal" id="powersSubclassLabel"></span>
        </h2>
        <div class="flex gap-3">
            <button type="button" onclick="selectAll()"
                class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-lg font-medium transition">
                ✅ Select All
            </button>
            <button type="button" onclick="deselectAll()"
                class="text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-1 rounded-lg font-medium transition">
                ✖ Deselect All
            </button>
        </div>
    </div>

    <div class="p-4" id="powersLoadingMsg" style="display:none">
        <p class="text-center text-gray-400 text-sm">⏳ Loading powers...</p>
    </div>

    <div class="p-4">
        <div id="powersGrid" class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-2"></div>
        <p id="powersEmpty" class="text-center text-gray-400 py-4 text-sm hidden">
            No powers found for this subclass. Please generate powers first.
        </p>
    </div>

    <div class="px-4 pb-3 border-t pt-3 flex justify-between items-center">
        <p class="text-sm text-gray-500">
            Selected: <span id="selectedCount" class="font-bold text-blue-700">0</span> powers
        </p>
        <p class="text-xs text-gray-400" id="totalPowersCount"></p>
    </div>
</div>

{{-- Submit --}}
<div id="submitSection" style="display:none">
    <button type="submit"
        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-xl transition text-sm">
        ➕ Add Stock for Selected Powers
    </button>
</div>

</form>

<script>
function siLoadClasses(categoryId) {
    document.getElementById('si_class').innerHTML = '<option value="">-- Select Class --</option>';
    document.getElementById('si_subclass').innerHTML = '<option value="">-- Select Subclass --</option>';
    hidePowers();
    if (!categoryId) return;

    fetch(`/api/classes/${categoryId}`)
        .then(r => r.json())
        .then(classes => {
            const sel = document.getElementById('si_class');
            classes.forEach(c => {
                sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
}

function siLoadSubclasses(classId) {
    document.getElementById('si_subclass').innerHTML = '<option value="">-- Select Subclass --</option>';
    hidePowers();
    if (!classId) return;

    fetch(`/api/subclasses/${classId}`)
        .then(r => r.json())
        .then(subs => {
            const sel = document.getElementById('si_subclass');
            subs.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
        });
}

function siLoadPowers(subclassId) {
    hidePowers();
    if (!subclassId) return;

    // Show loading
    document.getElementById('powersSection').style.display = 'block';
    document.getElementById('powersLoadingMsg').style.display = 'block';
    document.getElementById('powersGrid').innerHTML = '';
    document.getElementById('powersEmpty').classList.add('hidden');

    const subclassText = document.getElementById('si_subclass').options[
        document.getElementById('si_subclass').selectedIndex
    ].text;
    document.getElementById('powersSubclassLabel').textContent = '— ' + subclassText;

    fetch(`/api/powers/${subclassId}`)
        .then(r => r.json())
        .then(powers => {
            document.getElementById('powersLoadingMsg').style.display = 'none';

            if (powers.length === 0) {
                document.getElementById('powersEmpty').classList.remove('hidden');
                document.getElementById('submitSection').style.display = 'none';
                return;
            }

            document.getElementById('totalPowersCount').textContent = powers.length + ' powers available';

            const grid = document.getElementById('powersGrid');
            grid.innerHTML = '';

            powers.forEach(p => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-1.5 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded-lg px-2 py-2 cursor-pointer transition';
                label.innerHTML = `
                    <input type="checkbox" name="power_ids[]" value="${p.id}"
                        onchange="updateCount()"
                        class="accent-blue-700 w-4 h-4 flex-shrink-0">
                    <span class="text-xs font-medium text-gray-700">${p.label}</span>
                `;
                grid.appendChild(label);
            });

            document.getElementById('submitSection').style.display = 'block';
            updateCount();
        })
        .catch(err => {
            document.getElementById('powersLoadingMsg').style.display = 'none';
            document.getElementById('powersEmpty').classList.remove('hidden');
            document.getElementById('powersEmpty').textContent = 'Error loading powers. Please try again.';
            console.error('Powers load error:', err);
        });
}

function hidePowers() {
    document.getElementById('powersSection').style.display = 'none';
    document.getElementById('submitSection').style.display = 'none';
    document.getElementById('powersGrid').innerHTML = '';
    document.getElementById('selectedCount').textContent = '0';
}

function selectAll() {
    document.querySelectorAll('input[name="power_ids[]"]').forEach(cb => cb.checked = true);
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('input[name="power_ids[]"]').forEach(cb => cb.checked = false);
    updateCount();
}

function updateCount() {
    const count = document.querySelectorAll('input[name="power_ids[]"]:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

document.getElementById('stockInForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('input[name="power_ids[]"]:checked').length;
    if (checked === 0) {
        e.preventDefault();
        alert('Please select at least one power!');
    }
});
</script>

@endsection