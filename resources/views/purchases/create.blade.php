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

    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 mb-3 items-end">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-xs text-gray-500 mb-1">Product</label>
            <select id="sel_product" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Select</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-xs text-gray-500 mb-1">Power</label>
            <button type="button" onclick="openPowerModal()"
                class="w-full border border-blue-400 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg px-3 py-2 text-sm font-medium transition text-left">
                🔍 Select Power
            </button>
            <input type="hidden" id="sel_power_id">
            <p id="sel_power_label" class="text-xs text-gray-500 mt-1">No power selected</p>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Qty</label>
            <input type="number" id="sel_qty" min="1" value="1"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">LP Price</label>
            <input type="number" id="sel_lp_price" step="0.01" value="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Retail Price</label>
            <input type="number" id="sel_retail_price" step="0.01" value="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <button type="button" onclick="addItem()"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                + Add
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

{{-- ===== POWER MODAL ===== --}}
<div id="powerModal" class="fixed inset-0 z-50 hidden" onclick="closePowerModalOutside(event)">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white/80 backdrop-blur-xl border border-white/40 shadow-2xl rounded-3xl w-full max-w-2xl max-h-[80vh] flex flex-col"
            style="box-shadow: 0 25px 60px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.6);">

            {{-- Modal Header --}}
            <div class="p-5 border-b border-white/30 flex justify-between items-center flex-shrink-0">
                <h3 class="font-bold text-gray-800 text-lg">🔍 Select Power</h3>
                <button type="button" onclick="closePowerModal()"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            {{-- Filters --}}
            <div class="p-4 border-b border-white/30 flex-shrink-0">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Category</label>
                        <select id="modal_category" onchange="modalLoadClasses(this.value)"
                            class="w-full border border-gray-200 bg-white/70 rounded-xl px-3 py-2 text-sm backdrop-blur">
                            <option value="">-- Select --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Class</label>
                        <select id="modal_class" onchange="modalLoadSubclasses(this.value)"
                            class="w-full border border-gray-200 bg-white/70 rounded-xl px-3 py-2 text-sm backdrop-blur">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Subclass</label>
                        <select id="modal_subclass" onchange="modalLoadPowers(this.value)"
                            class="w-full border border-gray-200 bg-white/70 rounded-xl px-3 py-2 text-sm backdrop-blur">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Powers Grid --}}
            <div class="flex-1 overflow-y-auto p-4">
                <div id="modal_powers_loading" class="text-center text-gray-400 py-8 hidden">⏳ Loading...</div>
                <div id="modal_powers_empty" class="text-center text-gray-400 py-8 hidden">
                    No powers found. Select a subclass first.
                </div>
                <div id="modal_powers_grid" class="grid grid-cols-4 sm:grid-cols-5 gap-2"></div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 border-t border-white/30 flex-shrink-0">
                <button type="button" onclick="confirmPowerSelection()"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                    ✅ Confirm Selection
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let items = [];
let itemCounter = 0;
let selectedPowerId = null;
let selectedPowerLabel = null;

// ===== MODAL FUNCTIONS =====
function openPowerModal() {
    document.getElementById('powerModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePowerModal() {
    document.getElementById('powerModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function closePowerModalOutside(e) {
    if (e.target === e.currentTarget) closePowerModal();
}

function modalLoadClasses(categoryId) {
    document.getElementById('modal_class').innerHTML = '<option value="">-- Select --</option>';
    document.getElementById('modal_subclass').innerHTML = '<option value="">-- Select --</option>';
    document.getElementById('modal_powers_grid').innerHTML = '';
    if (!categoryId) return;

    fetch(`/api/classes/${categoryId}`)
        .then(r => r.json())
        .then(classes => {
            const sel = document.getElementById('modal_class');
            classes.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
        });
}

function modalLoadSubclasses(classId) {
    document.getElementById('modal_subclass').innerHTML = '<option value="">-- Select --</option>';
    document.getElementById('modal_powers_grid').innerHTML = '';
    if (!classId) return;

    fetch(`/api/subclasses/${classId}`)
        .then(r => r.json())
        .then(subs => {
            const sel = document.getElementById('modal_subclass');
            subs.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
        });
}

function modalLoadPowers(subclassId) {
    const grid = document.getElementById('modal_powers_grid');
    const loading = document.getElementById('modal_powers_loading');
    const empty = document.getElementById('modal_powers_empty');

    grid.innerHTML = '';
    loading.classList.remove('hidden');
    empty.classList.add('hidden');

    if (!subclassId) { loading.classList.add('hidden'); return; }

    fetch(`/api/powers/${subclassId}`)
        .then(r => r.json())
        .then(powers => {
            loading.classList.add('hidden');
            if (powers.length === 0) { empty.classList.remove('hidden'); return; }

            powers.forEach(p => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.id = p.id;
                btn.dataset.label = p.label;
                btn.className = 'power-btn border border-gray-200 bg-white/60 hover:bg-blue-50 hover:border-blue-400 rounded-xl px-2 py-2 text-xs font-medium text-gray-700 transition backdrop-blur';
                btn.textContent = p.label;
                btn.onclick = () => selectModalPower(btn, p.id, p.label);
                grid.appendChild(btn);
            });
        });
}

function selectModalPower(btn, id, label) {
    // Deselect previous
    document.querySelectorAll('.power-btn').forEach(b => {
        b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
        b.classList.add('bg-white/60', 'text-gray-700', 'border-gray-200');
    });

    // Select current
    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
    btn.classList.remove('bg-white/60', 'text-gray-700', 'border-gray-200');

    selectedPowerId = id;
    selectedPowerLabel = label;
}

function confirmPowerSelection() {
    if (!selectedPowerId) { alert('Please select a power first!'); return; }

    document.getElementById('sel_power_id').value = selectedPowerId;
    document.getElementById('sel_power_label').textContent = '✅ ' + selectedPowerLabel;
    closePowerModal();
}

// ===== ITEM FUNCTIONS =====
function addItem() {
    const productEl = document.getElementById('sel_product');
    const productId = productEl.value;
    const productName = productEl.options[productEl.selectedIndex].text;
    const powerId = document.getElementById('sel_power_id').value;
    const powerLabel = document.getElementById('sel_power_label').textContent.replace('✅ ', '');
    const qty = parseInt(document.getElementById('sel_qty').value);
    const lpPrice = parseFloat(document.getElementById('sel_lp_price').value);
    const retailPrice = parseFloat(document.getElementById('sel_retail_price').value);

    if (!productId || !powerId || powerId === '' || qty < 1) {
        alert('Please select product, power and enter quantity.');
        return;
    }

    itemCounter++;
    const total = qty * lpPrice;
    items.push({ id: itemCounter, productId, productName, powerId, powerLabel, qty, lpPrice, retailPrice, total });

    renderItems();
    calculateTotals();

    // Reset
    document.getElementById('sel_qty').value = 1;
    document.getElementById('sel_lp_price').value = 0;
    document.getElementById('sel_retail_price').value = 0;
    document.getElementById('sel_power_id').value = '';
    document.getElementById('sel_power_label').textContent = 'No power selected';
    selectedPowerId = null;
    selectedPowerLabel = null;
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