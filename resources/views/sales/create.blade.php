@extends('layouts.app')

@section('title', 'New Sale')

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('sales.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Back</a>
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">New Sale</h1>
</div>

{{-- Validation Errors --}}
@if($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        @foreach($errors->all() as $error)
            <p>• {{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('sales.store') }}">
    @csrf

    {{-- Top Section: Customer, Date, Payment --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Invoice Date <span class="text-red-500">*</span></label>
                <input type="date" name="invoice_date" required
                    value="{{ old('invoice_date', date('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Payment Mode</label>
                <select name="payment_mode"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cash"   {{ old('payment_mode') == 'cash'   ? 'selected' : '' }}>Cash</option>
                    <option value="credit" {{ old('payment_mode') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="bank"   {{ old('payment_mode') == 'bank'   ? 'selected' : '' }}>Bank</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Sale Items</h2>
            <button type="button" id="add-item"
                class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded-lg text-xs font-medium">
                + Add Item
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="items-table">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs">
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-left">Power</th>
                        <th class="px-3 py-2 text-left w-24">Qty</th>
                        <th class="px-3 py-2 text-left w-32">Unit Price</th>
                        <th class="px-3 py-2 text-right w-28">Total</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    {{-- rows injected by JS --}}
                </tbody>
            </table>
            <p id="no-items-msg" class="text-center text-gray-400 text-sm py-6">
                No items added yet. Click <strong>"+ Add Item"</strong> to begin.
            </p>
        </div>
    </div>

    {{-- Totals --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 w-28">Discount (Rs.)</label>
                    <input type="number" name="discount" id="discount" min="0" step="0.01"
                        value="{{ old('discount', 0) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 w-28">Tax (Rs.)</label>
                    <input type="number" name="tax" id="tax" min="0" step="0.01"
                        value="{{ old('tax', 0) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 w-28">Paid (Rs.)</label>
                    <input type="number" name="paid" id="paid" min="0" step="0.01"
                        value="{{ old('paid', 0) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Gross Total</span><span id="summary-gross">Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Discount</span><span id="summary-discount">- Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax</span><span id="summary-tax">+ Rs. 0.00</span>
                </div>
                <div class="flex justify-between font-semibold text-gray-800 border-t pt-2">
                    <span>Net Total</span><span id="summary-net">Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Paid</span><span id="summary-paid">Rs. 0.00</span>
                </div>
                <div class="flex justify-between font-bold text-red-600 border-t pt-2">
                    <span>Balance</span><span id="summary-balance">Rs. 0.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
            class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
            Save Sale
        </button>
        <a href="{{ route('sales.index') }}"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition">
            Cancel
        </a>
    </div>
</form>

{{-- ═══════════════════════════════════════════════
     POWERS MODAL
═══════════════════════════════════════════════ --}}
<div id="powers-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div id="modal-backdrop" class="absolute inset-0 bg-black bg-opacity-40"></div>

    {{-- Modal Box --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Select Powers</h3>
                <p id="modal-product-name" class="text-xs text-blue-600 mt-0.5"></p>
            </div>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        {{-- Search --}}
        <div class="px-5 py-3 border-b">
            <input type="text" id="power-search" placeholder="🔍 Search power (e.g. +1.00, -2.50)..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Unit Price for this product --}}
        <div class="px-5 py-3 border-b bg-blue-50">
            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-600 font-medium whitespace-nowrap">Unit Price (Rs.)</label>
                <input type="number" id="modal-unit-price" min="0" step="0.01" placeholder="0.00"
                    class="border border-blue-300 rounded-lg px-3 py-1.5 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-xs text-gray-500">Applied to all selected powers (can edit per row after)</span>
            </div>
        </div>

        {{-- Powers List --}}
        <div class="flex-1 overflow-y-auto px-5 py-3">
            <div id="powers-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                {{-- injected by JS --}}
            </div>
            <p id="no-power-results" class="text-center text-gray-400 text-sm py-4 hidden">No powers match your search.</p>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-4 border-t flex items-center justify-between bg-gray-50 rounded-b-2xl">
            <span id="selected-count" class="text-sm text-blue-700 font-medium">0 powers selected</span>
            <div class="flex gap-2">
                <button id="modal-cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm">
                    Cancel
                </button>
                <button id="modal-confirm" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    Add to Sale
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════ --}}
<script>
// ── Data from PHP ──────────────────────────────────────────────────────────
const PRODUCTS = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
const POWERS   = @json($powers->map(fn($pw) => ['id' => $pw->id, 'label' => $pw->getLabel()]));

// ── State ──────────────────────────────────────────────────────────────────
let rowIdx        = 0;      // global row counter for unique input names
let modalRowIdx   = null;   // which row triggered the modal (null = new rows)
let modalProductId = null;

// ── Helpers ────────────────────────────────────────────────────────────────
const fmt = n => 'Rs. ' + parseFloat(n || 0).toFixed(2);

function productName(id) {
    const p = PRODUCTS.find(x => x.id == id);
    return p ? p.name : '—';
}
function powerLabel(id) {
    const p = POWERS.find(x => x.id == id);
    return p ? p.label : '—';
}

// ── Recalculate totals ─────────────────────────────────────────────────────
function recalcTotals() {
    let gross = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = qty * price;
        row.querySelector('.row-total').textContent = total.toFixed(2);
        gross += total;
    });
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax      = parseFloat(document.getElementById('tax').value)      || 0;
    const paid     = parseFloat(document.getElementById('paid').value)     || 0;
    const net      = gross - discount + tax;
    const balance  = net - paid;

    document.getElementById('summary-gross').textContent    = fmt(gross);
    document.getElementById('summary-discount').textContent = '- ' + fmt(discount);
    document.getElementById('summary-tax').textContent      = '+ ' + fmt(tax);
    document.getElementById('summary-net').textContent      = fmt(net);
    document.getElementById('summary-paid').textContent     = fmt(paid);
    document.getElementById('summary-balance').textContent  = fmt(balance);
}

['discount','tax','paid'].forEach(id =>
    document.getElementById(id).addEventListener('input', recalcTotals)
);

// ── Build a single table row ───────────────────────────────────────────────
function buildRow(productId, powerId, qty, unitPrice) {
    const i   = rowIdx++;
    const tr  = document.createElement('tr');
    tr.className = 'item-row border-t';
    tr.dataset.index = i;

    tr.innerHTML = `
        <td class="px-3 py-2">
            <input type="hidden" name="items[${i}][product_id]" value="${productId}">
            <span class="text-sm text-gray-800 font-medium">${productName(productId)}</span>
        </td>
        <td class="px-3 py-2">
            <input type="hidden" name="items[${i}][power_id]" value="${powerId || ''}">
            <button type="button"
                class="open-power-modal text-xs bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 border border-gray-300 rounded px-2 py-1 transition"
                data-row="${i}" data-product="${productId}">
                ${powerId ? powerLabel(powerId) : '-- Power --'}
            </button>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][quantity]" min="1" required
                value="${qty || ''}" placeholder="0"
                class="qty-input w-20 border border-gray-300 rounded px-2 py-1.5 text-sm">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][unit_price]" min="0" step="0.01" required
                value="${unitPrice || ''}" placeholder="0.00"
                class="price-input w-28 border border-gray-300 rounded px-2 py-1.5 text-sm">
        </td>
        <td class="px-3 py-2 text-right">
            <span class="row-total text-gray-700 font-medium">0.00</span>
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="remove-row text-red-400 hover:text-red-600 text-xl leading-none">×</button>
        </td>`;

    tr.querySelector('.qty-input').addEventListener('input', recalcTotals);
    tr.querySelector('.price-input').addEventListener('input', recalcTotals);
    return tr;
}

function refreshNoItemsMsg() {
    const hasRows = document.querySelectorAll('.item-row').length > 0;
    document.getElementById('no-items-msg').classList.toggle('hidden', hasRows);
}

// ── Remove row ─────────────────────────────────────────────────────────────
document.getElementById('items-body').addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        refreshNoItemsMsg();
        recalcTotals();
    }
    if (e.target.classList.contains('open-power-modal')) {
        const row = e.target.closest('tr');
        openModal(parseInt(e.target.dataset.product), row.dataset.index);
    }
});

// ── "+ Add Item" opens modal for new rows ──────────────────────────────────
document.getElementById('add-item').addEventListener('click', () => {
    openModal(null, null);
});

// ════════════════════════════════════════════════
//  MODAL LOGIC
// ════════════════════════════════════════════════
const modal          = document.getElementById('powers-modal');
const powersList     = document.getElementById('powers-list');
const powerSearch    = document.getElementById('power-search');
const selectedCount  = document.getElementById('selected-count');
const modalProductEl = document.getElementById('modal-product-name');
const modalUnitPrice = document.getElementById('modal-unit-price');

let allPowerItems = []; // references to rendered power cards

function openModal(productId, existingRowIdx) {
    modalProductId = productId;
    modalRowIdx    = existingRowIdx; // null = add new rows

    // Header label
    modalProductEl.textContent = productId ? productName(productId) : '';
    modalUnitPrice.value = '';
    powerSearch.value    = '';

    renderPowerList('');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    powerSearch.focus();
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    allPowerItems = [];
}

function renderPowerList(filter) {
    powersList.innerHTML = '';
    allPowerItems = [];

    const lower = filter.toLowerCase();
    let visible = 0;

    POWERS.forEach(pw => {
        if (lower && !pw.label.toLowerCase().includes(lower)) return;
        visible++;

        const div = document.createElement('div');
        div.className = 'power-card flex items-center gap-3 border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition select-none';
        div.dataset.id = pw.id;

        div.innerHTML = `
            <input type="checkbox" class="power-check w-4 h-4 accent-blue-600 cursor-pointer" data-id="${pw.id}">
            <span class="text-sm text-gray-700 flex-1">${pw.label}</span>
            <input type="number" class="power-qty w-16 border border-gray-300 rounded px-2 py-1 text-xs text-center hidden" 
                min="1" placeholder="Qty" data-id="${pw.id}">`;

        const check = div.querySelector('.power-check');
        const qtyIn = div.querySelector('.power-qty');

        // Click anywhere on card toggles checkbox
        div.addEventListener('click', e => {
            if (e.target === qtyIn) return; // don't toggle when clicking qty
            check.checked = !check.checked;
            toggleCard(div, check, qtyIn);
        });
        check.addEventListener('click', e => e.stopPropagation());
        check.addEventListener('change', () => toggleCard(div, check, qtyIn));

        powersList.appendChild(div);
        allPowerItems.push({ div, check, qtyIn, pw });
    });

    document.getElementById('no-power-results').classList.toggle('hidden', visible > 0);
    updateSelectedCount();
}

function toggleCard(div, check, qtyIn) {
    if (check.checked) {
        div.classList.add('border-blue-500', 'bg-blue-50');
        qtyIn.classList.remove('hidden');
        qtyIn.value = 1;
        qtyIn.focus();
    } else {
        div.classList.remove('border-blue-500', 'bg-blue-50');
        qtyIn.classList.add('hidden');
        qtyIn.value = '';
    }
    updateSelectedCount();
}

function updateSelectedCount() {
    const n = allPowerItems.filter(x => x.check.checked).length;
    selectedCount.textContent = n + ' power' + (n !== 1 ? 's' : '') + ' selected';
}

// Search
powerSearch.addEventListener('input', () => renderPowerList(powerSearch.value));

// Close buttons
document.getElementById('modal-close').addEventListener('click', closeModal);
document.getElementById('modal-cancel').addEventListener('click', closeModal);
document.getElementById('modal-backdrop').addEventListener('click', closeModal);

// ── Confirm: add rows ──────────────────────────────────────────────────────
document.getElementById('modal-confirm').addEventListener('click', () => {
    const selected = allPowerItems.filter(x => x.check.checked);
    if (!selected.length) { alert('Koi power select nahi ki!'); return; }

    const unitPrice = parseFloat(modalUnitPrice.value) || 0;

    // Need a product — if not pre-set, ask user to pick from modal header
    // For "Add Item" flow: we need product selection too
    // We'll handle: if modalProductId is null, inject a product-select row group
    if (modalProductId === null) {
        // show product picker step
        alert('Pehle product select karein "Add Item" se.');
        closeModal();
        openProductPickerThenModal();
        return;
    }

    const tbody = document.getElementById('items-body');

    selected.forEach(({ pw, qtyIn }) => {
        const qty = parseInt(qtyIn.value) || 1;
        const tr  = buildRow(modalProductId, pw.id, qty, unitPrice);
        tbody.appendChild(tr);
    });

    recalcTotals();
    refreshNoItemsMsg();
    closeModal();
});

// ════════════════════════════════════════════════
//  PRODUCT → POWERS FLOW (Add Item button)
// ════════════════════════════════════════════════
function openProductPickerThenModal() {
    // Build a quick product picker modal inline
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-40';
    overlay.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Select Product</h3>
            <input type="text" id="prod-search" placeholder="🔍 Search product..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div id="prod-list" class="space-y-1 max-h-64 overflow-y-auto"></div>
            <button id="prod-cancel" class="mt-4 w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg text-sm">
                Cancel
            </button>
        </div>`;
    document.body.appendChild(overlay);

    function renderProds(filter) {
        const list  = overlay.querySelector('#prod-list');
        const lower = filter.toLowerCase();
        list.innerHTML = '';
        PRODUCTS.filter(p => !lower || p.name.toLowerCase().includes(lower)).forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 text-sm text-gray-700 hover:text-blue-700 transition';
            btn.textContent = p.name;
            btn.addEventListener('click', () => {
                document.body.removeChild(overlay);
                modalProductId = p.id;
                openModal(p.id, null);
            });
            list.appendChild(btn);
        });
    }

    renderProds('');
    overlay.querySelector('#prod-search').addEventListener('input', e => renderProds(e.target.value));
    overlay.querySelector('#prod-cancel').addEventListener('click', () => document.body.removeChild(overlay));
    overlay.querySelector('#prod-search').focus();
}

// Override "Add Item" to go through product picker
document.getElementById('add-item').removeEventListener('click', () => {});
document.getElementById('add-item').addEventListener('click', () => {
    openProductPickerThenModal();
});

refreshNoItemsMsg();
</script>

@endsection