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

    {{-- ── Row 1: Customer, Salesman, Price Type ── --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">

            {{-- Customer --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" id="customer_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}"
                            data-balance="{{ $c->current_balance ?? 0 }}"
                            {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Salesman --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1">Salesman</label>
                <select name="salesman_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Salesman --</option>
                    @foreach($salesmen as $u)
                        <option value="{{ $u->id }}" {{ old('salesman_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Price Type --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Price Type</label>
                <div class="flex gap-3">
                    @foreach(['retail' => 'Retail', 'wholesale' => 'Wholesale', 'company' => 'Company'] as $val => $label)
                        <label class="flex items-center gap-1.5 cursor-pointer text-sm text-gray-700">
                            <input type="radio" name="price_type" value="{{ $val }}"
                                {{ old('price_type', 'wholesale') == $val ? 'checked' : '' }}
                                class="accent-blue-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Row 2: Invoice Date, Due Date, Payment Mode --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">

            <div>
                <label class="block text-xs text-gray-500 mb-1">Invoice Date <span class="text-red-500">*</span></label>
                <input type="date" name="invoice_date" required
                    value="{{ old('invoice_date', date('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Due Date</label>
                <input type="date" name="due_date"
                    value="{{ old('due_date') }}"
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

        {{-- Memo --}}
        <div>
            <label class="block text-xs text-gray-500 mb-1">Memo</label>
            <input type="text" name="memo" value="{{ old('memo') }}" placeholder="Optional note..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    {{-- ── Items Table ── --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Sale Items</h2>
            <button type="button" id="add-item"
                class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded-lg text-xs font-medium">
                + Add Item
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs">
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-left">Power</th>
                        <th class="px-3 py-2 text-left w-20">Qty</th>
                        <th class="px-3 py-2 text-left w-28">Unit Price</th>
                        <th class="px-3 py-2 text-left w-24">Item Disc.</th>
                        <th class="px-3 py-2 text-right w-28">Total</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="items-body"></tbody>
            </table>
            <p id="no-items-msg" class="text-center text-gray-400 text-sm py-6">
                No items added. Click <strong>"+ Add Item"</strong> to begin.
            </p>
        </div>
    </div>

    {{-- ── Totals ── --}}
    <div class="bg-white rounded-xl shadow p-5 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Inputs --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 w-32">Invoice Discount</label>
                    <input type="number" name="discount" id="discount" min="0" step="0.01"
                        value="{{ old('discount', 0) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 w-32">Tax (Rs.)</label>
                    <input type="number" name="tax" id="tax" min="0" step="0.01"
                        value="{{ old('tax', 0) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 w-32">Paid (Rs.)</label>
                    <input type="number" name="paid" id="paid" min="0" step="0.01"
                        value="{{ old('paid', 0) }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Gross Total</span><span id="summary-gross">Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Item Discounts</span><span id="summary-item-disc">- Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Invoice Discount</span><span id="summary-discount">- Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax</span><span id="summary-tax">+ Rs. 0.00</span>
                </div>
                <div class="flex justify-between font-semibold text-gray-800 border-t pt-2">
                    <span>Net Total</span><span id="summary-net">Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-gray-500 border-t pt-2">
                    <span>Pre Balance</span><span id="summary-pre-balance">Rs. 0.00</span>
                </div>
                <div class="flex justify-between font-semibold text-gray-700">
                    <span>Total Payable</span><span id="summary-total-payable">Rs. 0.00</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Paid</span><span id="summary-paid">Rs. 0.00</span>
                </div>
                <div class="flex justify-between font-bold text-red-600 border-t pt-2">
                    <span>New Balance</span><span id="summary-balance">Rs. 0.00</span>
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

{{-- ═══════════════════ POWERS MODAL ═══════════════════ --}}
<div id="powers-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="modal-backdrop" class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

        <div class="flex items-center justify-between px-5 py-4 border-b">
            <div>
                <h3 class="font-bold text-gray-800">Select Powers</h3>
                <p id="modal-product-name" class="text-xs text-blue-600 mt-0.5"></p>
            </div>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <div class="px-5 py-3 border-b">
            <input type="text" id="power-search" placeholder="🔍 Search power (e.g. +1.00, -2.50)..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="px-5 py-3 border-b bg-blue-50 flex items-center gap-3">
            <label class="text-sm text-gray-600 font-medium whitespace-nowrap">Unit Price (Rs.)</label>
            <input type="number" id="modal-unit-price" min="0" step="0.01" placeholder="0.00"
                class="border border-blue-300 rounded-lg px-3 py-1.5 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span class="text-xs text-gray-500">Applied to all selected powers</span>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-3">
            <div id="powers-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
            <p id="no-power-results" class="text-center text-gray-400 text-sm py-4 hidden">No powers match your search.</p>
        </div>

        <div class="px-5 py-4 border-t flex items-center justify-between bg-gray-50 rounded-b-2xl">
            <span id="selected-count" class="text-sm text-blue-700 font-medium">0 powers selected</span>
            <div class="flex gap-2">
                <button id="modal-cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm">Cancel</button>
                <button id="modal-confirm" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium">Add to Sale</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════ PRODUCT PICKER MODAL ═══════════════════ --}}
<div id="product-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div id="product-backdrop" class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm flex flex-col max-h-[80vh]">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="font-bold text-gray-800">Select Product</h3>
            <button id="product-close" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="px-5 py-3 border-b">
            <input type="text" id="product-search" placeholder="🔍 Search product..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div id="product-list" class="flex-1 overflow-y-auto px-3 py-2 space-y-1"></div>
    </div>
</div>

<script>
// ── PHP Data ───────────────────────────────────────────────────────────────
const PRODUCTS = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
const POWERS   = @json($powers->map(fn($pw) => ['id' => $pw->id, 'label' => $pw->getLabel()]));

// ── State ──────────────────────────────────────────────────────────────────
let rowIdx         = 0;
let modalProductId = null;
let allPowerItems  = [];

// ── Helpers ────────────────────────────────────────────────────────────────
const fmt         = n  => 'Rs. ' + parseFloat(n || 0).toFixed(2);
const productName = id => (PRODUCTS.find(x => x.id == id) || {}).name || '—';
const powerLabel  = id => (POWERS.find(x => x.id == id)   || {}).label || '—';

// ── Customer pre-balance ───────────────────────────────────────────────────
let preBalance = 0;
document.getElementById('customer_id').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    preBalance = parseFloat(opt.dataset.balance || 0);
    recalcTotals();
});

// ── Recalculate totals ─────────────────────────────────────────────────────
function recalcTotals() {
    let gross    = 0;
    let itemDisc = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const disc  = parseFloat(row.querySelector('.disc-input').value)  || 0;
        const total = qty * price - disc;
        row.querySelector('.row-total').textContent = total.toFixed(2);
        gross    += qty * price;
        itemDisc += disc;
    });

    const invDiscount  = parseFloat(document.getElementById('discount').value) || 0;
    const tax          = parseFloat(document.getElementById('tax').value)       || 0;
    const paid         = parseFloat(document.getElementById('paid').value)      || 0;
    const net          = gross - itemDisc - invDiscount + tax;
    const totalPayable = net + preBalance;
    const newBalance   = totalPayable - paid;

    document.getElementById('summary-gross').textContent        = fmt(gross);
    document.getElementById('summary-item-disc').textContent    = '- ' + fmt(itemDisc);
    document.getElementById('summary-discount').textContent     = '- ' + fmt(invDiscount);
    document.getElementById('summary-tax').textContent          = '+ ' + fmt(tax);
    document.getElementById('summary-net').textContent          = fmt(net);
    document.getElementById('summary-pre-balance').textContent  = fmt(preBalance);
    document.getElementById('summary-total-payable').textContent= fmt(totalPayable);
    document.getElementById('summary-paid').textContent         = fmt(paid);
    document.getElementById('summary-balance').textContent      = fmt(newBalance);
}

['discount','tax','paid'].forEach(id =>
    document.getElementById(id).addEventListener('input', recalcTotals)
);

// ── Build table row ────────────────────────────────────────────────────────
function buildRow(productId, powerId, qty, unitPrice) {
    const i  = rowIdx++;
    const tr = document.createElement('tr');
    tr.className    = 'item-row border-t';
    tr.dataset.index = i;
    tr.innerHTML = `
        <td class="px-3 py-2">
            <input type="hidden" name="items[${i}][product_id]" value="${productId}">
            <span class="text-sm text-gray-800 font-medium">${productName(productId)}</span>
        </td>
        <td class="px-3 py-2">
            <input type="hidden" name="items[${i}][power_id]" value="${powerId || ''}">
            <span class="power-label text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded px-2 py-1">
                ${powerId ? powerLabel(powerId) : '—'}
            </span>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][quantity]" min="1" required
                value="${qty || 1}" placeholder="0"
                class="qty-input w-16 border border-gray-300 rounded px-2 py-1.5 text-sm text-center">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][unit_price]" min="0" step="0.01" required
                value="${unitPrice || ''}" placeholder="0.00"
                class="price-input w-24 border border-gray-300 rounded px-2 py-1.5 text-sm">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][discount]" min="0" step="0.01"
                value="0" placeholder="0.00"
                class="disc-input w-20 border border-gray-300 rounded px-2 py-1.5 text-sm">
        </td>
        <td class="px-3 py-2 text-right font-medium text-gray-700">
            <span class="row-total">0.00</span>
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="remove-row text-red-400 hover:text-red-600 text-xl leading-none">×</button>
        </td>`;

    tr.querySelector('.qty-input').addEventListener('input', recalcTotals);
    tr.querySelector('.price-input').addEventListener('input', recalcTotals);
    tr.querySelector('.disc-input').addEventListener('input', recalcTotals);
    return tr;
}

function refreshMsg() {
    const has = document.querySelectorAll('.item-row').length > 0;
    document.getElementById('no-items-msg').classList.toggle('hidden', has);
}

// Remove row
document.getElementById('items-body').addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        refreshMsg();
        recalcTotals();
    }
});

// ════════════════════════════════════════════════
//  PRODUCT PICKER MODAL
// ════════════════════════════════════════════════
const productModal    = document.getElementById('product-modal');
const productList     = document.getElementById('product-list');
const productSearchEl = document.getElementById('product-search');

function openProductModal() {
    productSearchEl.value = '';
    renderProductList('');
    productModal.classList.remove('hidden');
    productModal.classList.add('flex');
    productSearchEl.focus();
}
function closeProductModal() {
    productModal.classList.add('hidden');
    productModal.classList.remove('flex');
}

function renderProductList(filter) {
    productList.innerHTML = '';
    const lower = filter.toLowerCase();
    PRODUCTS.filter(p => !lower || p.name.toLowerCase().includes(lower)).forEach(p => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 text-sm text-gray-700 hover:text-blue-700 transition';
        btn.textContent = p.name;
        btn.addEventListener('click', () => {
            closeProductModal();
            openPowersModal(p.id);
        });
        productList.appendChild(btn);
    });
}

productSearchEl.addEventListener('input', e => renderProductList(e.target.value));
document.getElementById('product-close').addEventListener('click', closeProductModal);
document.getElementById('product-backdrop').addEventListener('click', closeProductModal);

document.getElementById('add-item').addEventListener('click', openProductModal);

// ════════════════════════════════════════════════
//  POWERS MODAL
// ════════════════════════════════════════════════
const powersModal    = document.getElementById('powers-modal');
const powersList     = document.getElementById('powers-list');
const powerSearchEl  = document.getElementById('power-search');
const selectedCount  = document.getElementById('selected-count');
const modalUnitPrice = document.getElementById('modal-unit-price');

function openPowersModal(productId) {
    modalProductId = productId;
    document.getElementById('modal-product-name').textContent = productName(productId);
    modalUnitPrice.value = '';
    powerSearchEl.value  = '';
    renderPowerList('');
    powersModal.classList.remove('hidden');
    powersModal.classList.add('flex');
    powerSearchEl.focus();
}
function closePowersModal() {
    powersModal.classList.add('hidden');
    powersModal.classList.remove('flex');
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
        div.className = 'flex items-center gap-3 border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition select-none';
        div.innerHTML = `
            <input type="checkbox" class="power-check w-4 h-4 accent-blue-600 cursor-pointer" data-id="${pw.id}">
            <span class="text-sm text-gray-700 flex-1">${pw.label}</span>
            <input type="number" class="power-qty w-16 border border-gray-300 rounded px-2 py-1 text-xs text-center hidden" min="1" placeholder="Qty">`;

        const check = div.querySelector('.power-check');
        const qtyIn = div.querySelector('.power-qty');

        div.addEventListener('click', e => {
            if (e.target === qtyIn || e.target === check) return;
            check.checked = !check.checked;
            toggleCard(div, check, qtyIn);
        });
        check.addEventListener('change', () => toggleCard(div, check, qtyIn));

        powersList.appendChild(div);
        allPowerItems.push({ div, check, qtyIn, pw });
    });

    document.getElementById('no-power-results').classList.toggle('hidden', visible > 0);
    updateCount();
}

function toggleCard(div, check, qtyIn) {
    if (check.checked) {
        div.classList.add('border-blue-500', 'bg-blue-50');
        qtyIn.classList.remove('hidden');
        qtyIn.value = 1;
    } else {
        div.classList.remove('border-blue-500', 'bg-blue-50');
        qtyIn.classList.add('hidden');
        qtyIn.value = '';
    }
    updateCount();
}

function updateCount() {
    const n = allPowerItems.filter(x => x.check.checked).length;
    selectedCount.textContent = n + ' power' + (n !== 1 ? 's' : '') + ' selected';
}

powerSearchEl.addEventListener('input', e => renderPowerList(e.target.value));
document.getElementById('modal-close').addEventListener('click', closePowersModal);
document.getElementById('modal-cancel').addEventListener('click', closePowersModal);
document.getElementById('modal-backdrop').addEventListener('click', closePowersModal);

// Confirm — add rows
document.getElementById('modal-confirm').addEventListener('click', () => {
    const selected = allPowerItems.filter(x => x.check.checked);
    if (!selected.length) { alert('Koi power select nahi ki!'); return; }

    const unitPrice = parseFloat(modalUnitPrice.value) || 0;
    const tbody     = document.getElementById('items-body');

    selected.forEach(({ pw, qtyIn }) => {
        const qty = parseInt(qtyIn.value) || 1;
        tbody.appendChild(buildRow(modalProductId, pw.id, qty, unitPrice));
    });

    recalcTotals();
    refreshMsg();
    closePowersModal();
});

refreshMsg();
</script>

@endsection