@extends('layouts.app')
@section('title', 'New Purchase')

@section('content')

<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">New Purchase Entry</h1>

<form method="POST" action="/purchases" id="purchaseForm">
@csrf

{{-- Header --}}
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice #</label>
            <input type="text" name="invoice_no" value="{{ $invoiceNo }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
            <select name="supplier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
            <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Memo</label>
        <input type="text" name="memo" placeholder="Optional note..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
</div>

{{-- Add Items --}}
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4">Add Items</h2>

    {{-- Product + Category + Class + Subclass --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select id="ai_product" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Select</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select id="ai_category" onchange="aiLoadClasses(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
            <select id="ai_class" onchange="aiLoadSubclasses(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Class --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subclass</label>
            <select id="ai_subclass" onchange="aiLoadPowers(this.value)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">-- Select Subclass --</option>
            </select>
        </div>
    </div>

    {{-- Powers grid with per-power qty/price --}}
    <div class="border border-gray-200 rounded-xl mb-4" id="ai_powersSection" style="display:none">
        <div class="p-3 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h3 class="font-medium text-gray-700 text-sm">
                Select Powers <span class="text-gray-400 font-normal" id="ai_subclassLabel"></span>
            </h3>
            <div class="flex gap-2">
                <button type="button" onclick="aiSelectAll()"
                    class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-lg font-medium transition">
                    ✅ Select All
                </button>
                <button type="button" onclick="aiDeselectAll()"
                    class="text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-1 rounded-lg font-medium transition">
                    ✖ Deselect All
                </button>
            </div>
        </div>

        <div class="p-3" id="ai_powersLoadingMsg" style="display:none">
            <p class="text-center text-gray-400 text-sm">⏳ Loading powers...</p>
        </div>

        <div class="p-3 max-h-80 overflow-y-auto">
            <div class="flex items-center gap-2 px-3 py-1 mb-1 text-xs font-medium text-gray-500" id="ai_powersHeader" style="display:none">
                <span class="w-4 flex-shrink-0"></span>
                <span class="w-20 flex-shrink-0">Power</span>
                <span class="w-20">Qty</span>
                <span class="w-24">LP Price</span>
                <span class="w-24">Retail Price</span>
            </div>
            <div id="ai_powersGrid" class="space-y-2"></div>
            <p id="ai_powersEmpty" class="text-center text-gray-400 py-4 text-sm hidden">
                No powers found for this subclass. Please generate powers first.
            </p>
        </div>

        <div class="px-3 pb-3 flex justify-between items-center">
            <p class="text-sm text-gray-500">
                Selected: <span id="ai_selectedCount" class="font-bold text-blue-700">0</span> powers
            </p>
            <button type="button" onclick="addSelectedPowers()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                + Add Selected to Purchase
            </button>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-xs">
                    <th class="px-3 py-2 text-left">#</th>
                    <th class="px-3 py-2 text-left">Product</th>
                    <th class="px-3 py-2 text-left">Power</th>
                    <th class="px-3 py-2 text-center">Qty</th>
                    <th class="px-3 py-2 text-right">LP Price</th>
                    <th class="px-3 py-2 text-right">Retail Price</th>
                    <th class="px-3 py-2 text-right">Total</th>
                    <th class="px-3 py-2 text-center">Remove</th>
                </tr>
            </thead>
            <tbody id="itemsBody">
                <tr id="emptyRow">
                    <td colspan="8" class="px-3 py-4 text-center text-gray-400 text-sm">No items added yet</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Totals --}}
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Gross Total:</span>
                <span class="font-bold" id="grossTotal">Rs. 0.00</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Discount:</span>
                <input type="number" name="discount" id="discount" value="0" step="0.01"
                    onchange="calculateTotals()"
                    class="w-32 border border-gray-300 rounded-lg px-3 py-1 text-sm text-right">
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Tax:</span>
                <input type="number" name="tax" id="tax" value="0" step="0.01"
                    onchange="calculateTotals()"
                    class="w-32 border border-gray-300 rounded-lg px-3 py-1 text-sm text-right">
            </div>
            <div class="flex justify-between items-center border-t pt-2">
                <span class="font-semibold text-gray-700">Net Total:</span>
                <span class="font-bold text-lg text-blue-700" id="netTotal">Rs. 0.00</span>
            </div>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Mode</label>
                <select name="payment_mode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="cash">Cash</option>
                    <option value="credit">Credit</option>
                    <option value="bank">Bank</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Paid Amount</label>
                <input type="number" name="paid" id="paid" value="0" step="0.01"
                    onchange="calculateBalance()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex justify-between items-center border-t pt-2">
                <span class="font-semibold text-gray-700">Balance:</span>
                <span class="font-bold text-lg text-red-600" id="balanceDisplay">Rs. 0.00</span>
            </div>
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit"
        class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
        💾 Save Purchase
    </button>
    <a href="/purchases"
        class="flex-1 text-center bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold py-3 rounded-lg transition">
        Cancel
    </a>
</div>

</form>

{{-- Power selection modal removed: power selection now happens inline in the Add Items section --}}

<script>
let items = [];
let itemCounter = 0;

// ===== ADD ITEMS: CATEGORY/CLASS/SUBCLASS CASCADE =====
function aiLoadClasses(categoryId) {
    document.getElementById('ai_class').innerHTML = '<option value="">-- Select Class --</option>';
    document.getElementById('ai_subclass').innerHTML = '<option value="">-- Select Subclass --</option>';
    aiHidePowers();
    if (!categoryId) return;

    fetch(`/api/classes/${categoryId}`)
        .then(r => r.json())
        .then(classes => {
            const sel = document.getElementById('ai_class');
            classes.forEach(c => {
                sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
}

function aiLoadSubclasses(classId) {
    document.getElementById('ai_subclass').innerHTML = '<option value="">-- Select Subclass --</option>';
    aiHidePowers();
    if (!classId) return;

    fetch(`/api/subclasses/${classId}`)
        .then(r => r.json())
        .then(subs => {
            const sel = document.getElementById('ai_subclass');
            subs.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
        });
}

function aiLoadPowers(subclassId) {
    aiHidePowers();
    if (!subclassId) return;

    document.getElementById('ai_powersSection').style.display = 'block';
    document.getElementById('ai_powersLoadingMsg').style.display = 'block';
    document.getElementById('ai_powersGrid').innerHTML = '';
    document.getElementById('ai_powersEmpty').classList.add('hidden');

    const subclassSelect = document.getElementById('ai_subclass');
    const subclassText = subclassSelect.options[subclassSelect.selectedIndex].text;
    document.getElementById('ai_subclassLabel').textContent = '— ' + subclassText;

    fetch(`/api/powers/${subclassId}`)
        .then(r => r.json())
        .then(powers => {
            document.getElementById('ai_powersLoadingMsg').style.display = 'none';

            if (powers.length === 0) {
                document.getElementById('ai_powersEmpty').classList.remove('hidden');
                document.getElementById('ai_powersHeader').style.display = 'none';
                return;
            }

            document.getElementById('ai_powersHeader').style.display = 'flex';

            const grid = document.getElementById('ai_powersGrid');
            grid.innerHTML = '';

            powers.forEach(p => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded-lg px-3 py-2 transition';
                row.innerHTML = `
                    <input type="checkbox" class="ai-power-checkbox accent-blue-700 w-4 h-4 flex-shrink-0"
                        data-id="${p.id}" data-label="${p.label}" onchange="aiUpdateCount()">
                    <span class="text-sm font-medium text-gray-700 w-20 flex-shrink-0">${p.label}</span>
                    <input type="number" class="ai-power-qty w-20 border border-gray-300 rounded-lg px-2 py-1 text-sm" min="1" value="1">
                    <input type="number" class="ai-power-lp w-24 border border-gray-300 rounded-lg px-2 py-1 text-sm" step="0.01" value="0">
                    <input type="number" class="ai-power-retail w-24 border border-gray-300 rounded-lg px-2 py-1 text-sm" step="0.01" value="0">
                `;
                grid.appendChild(row);
            });

            aiUpdateCount();
        })
        .catch(err => {
            document.getElementById('ai_powersLoadingMsg').style.display = 'none';
            document.getElementById('ai_powersEmpty').classList.remove('hidden');
            document.getElementById('ai_powersEmpty').textContent = 'Error loading powers. Please try again.';
            console.error('Powers load error:', err);
        });
}

function aiHidePowers() {
    document.getElementById('ai_powersSection').style.display = 'none';
    document.getElementById('ai_powersGrid').innerHTML = '';
    document.getElementById('ai_powersHeader').style.display = 'none';
    document.getElementById('ai_selectedCount').textContent = '0';
}

function aiSelectAll() {
    document.querySelectorAll('.ai-power-checkbox').forEach(cb => cb.checked = true);
    aiUpdateCount();
}

function aiDeselectAll() {
    document.querySelectorAll('.ai-power-checkbox').forEach(cb => cb.checked = false);
    aiUpdateCount();
}

function aiUpdateCount() {
    const count = document.querySelectorAll('.ai-power-checkbox:checked').length;
    document.getElementById('ai_selectedCount').textContent = count;
}

// ===== ADD SELECTED POWERS TO PURCHASE ITEMS TABLE =====
function addSelectedPowers() {
    const productEl = document.getElementById('ai_product');
    const productId = productEl.value;
    const productName = productEl.options[productEl.selectedIndex].text;

    if (!productId) {
        alert('Please select a product first.');
        return;
    }

    const checkedBoxes = document.querySelectorAll('.ai-power-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one power.');
        return;
    }

    let addedCount = 0;

    checkedBoxes.forEach(cb => {
        const row = cb.closest('div');
        const powerId = cb.dataset.id;
        const powerLabel = cb.dataset.label;
        const qty = parseInt(row.querySelector('.ai-power-qty').value) || 0;
        const lpPrice = parseFloat(row.querySelector('.ai-power-lp').value) || 0;
        const retailPrice = parseFloat(row.querySelector('.ai-power-retail').value) || 0;

        if (qty < 1) return;

        itemCounter++;
        const total = qty * lpPrice;
        items.push({ id: itemCounter, productId, productName, powerId, powerLabel, qty, lpPrice, retailPrice, total });
        addedCount++;
    });

    if (addedCount === 0) {
        alert('Please enter a valid quantity (1 or more) for the selected powers.');
        return;
    }

    renderItems();
    calculateTotals();

    // Reset the power selection grid for the next batch
    aiHidePowers();
    document.getElementById('ai_category').value = '';
    document.getElementById('ai_class').innerHTML = '<option value="">-- Select Class --</option>';
    document.getElementById('ai_subclass').innerHTML = '<option value="">-- Select Subclass --</option>';
}

function removeItem(id) {
    items = items.filter(i => i.id !== id);
    renderItems();
    calculateTotals();
}

function renderItems() {
    const tbody = document.getElementById('itemsBody');
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-4 text-center text-gray-400 text-sm">No items added yet</td></tr>';
        return;
    }

    let html = '';
    items.forEach((item, index) => {
        html += `
        <tr class="border-b">
            <td class="px-3 py-2 text-gray-400 text-xs">${index + 1}</td>
            <td class="px-3 py-2 text-sm">${item.productName}
                <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
            </td>
            <td class="px-3 py-2 text-sm">${item.powerLabel}
                <input type="hidden" name="items[${index}][power_id]" value="${item.powerId}">
            </td>
            <td class="px-3 py-2 text-center text-sm">
                ${item.qty}
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            </td>
            <td class="px-3 py-2 text-right text-sm">
                Rs. ${item.lpPrice.toFixed(2)}
                <input type="hidden" name="items[${index}][unit_price]" value="${item.lpPrice}">
                <input type="hidden" name="items[${index}][lp_price]" value="${item.lpPrice}">
            </td>
            <td class="px-3 py-2 text-right text-sm">
                Rs. ${item.retailPrice.toFixed(2)}
                <input type="hidden" name="items[${index}][retail_price]" value="${item.retailPrice}">
            </td>
            <td class="px-3 py-2 text-right font-medium text-sm">Rs. ${item.total.toFixed(2)}</td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeItem(${item.id})"
                    class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

function calculateTotals() {
    const gross = items.reduce((sum, i) => sum + i.total, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const net = gross - discount + tax;

    document.getElementById('grossTotal').textContent = 'Rs. ' + gross.toFixed(2);
    document.getElementById('netTotal').textContent = 'Rs. ' + net.toFixed(2);
    calculateBalance();
}

function calculateBalance() {
    const net = parseFloat(document.getElementById('netTotal').textContent.replace('Rs. ', '')) || 0;
    const paid = parseFloat(document.getElementById('paid').value) || 0;
    const bal = net - paid;

    document.getElementById('balanceDisplay').textContent = 'Rs. ' + bal.toFixed(2);
    document.getElementById('balanceDisplay').className =
        'font-bold text-lg ' + (bal > 0 ? 'text-red-600' : 'text-green-600');
}

document.getElementById('purchaseForm').addEventListener('submit', function(e) {
    if (items.length === 0) {
        e.preventDefault();
        alert('Please add at least one item before saving.');
    }
});
</script>

@endsection