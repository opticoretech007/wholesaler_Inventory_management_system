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

{{-- Add Item Row --}}
<div class="bg-white rounded-xl shadow p-6 mb-4">
    <h2 class="font-semibold text-gray-700 mb-4">Add Items</h2>

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-3">
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
            <select id="sel_power" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Select</option>
                @foreach($powers as $power)
                    <option value="{{ $power->id }}">{{ $power->getLabel() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Qty</label>
            <input type="number" id="sel_qty" min="1" value="1"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Unit Price</label>
            <input type="number" id="sel_price" step="0.01" value="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button type="button" onclick="addItem()"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                + Add
            </button>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="itemsTable">
            <thead>
                <tr class="bg-gray-100 text-gray-600">
                    <th class="px-3 py-2 text-left">#</th>
                    <th class="px-3 py-2 text-left">Product</th>
                    <th class="px-3 py-2 text-left">Power</th>
                    <th class="px-3 py-2 text-center">Qty</th>
                    <th class="px-3 py-2 text-right">Unit Price</th>
                    <th class="px-3 py-2 text-right">Total</th>
                    <th class="px-3 py-2 text-center">Remove</th>
                </tr>
            </thead>
            <tbody id="itemsBody">
                <tr id="emptyRow">
                    <td colspan="7" class="px-3 py-4 text-center text-gray-400">No items added yet</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Totals + Payment --}}
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

{{-- Submit --}}
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

<script>
let items = [];
let itemCounter = 0;

const products = @json($products);
const powers = @json($powers->map(fn($p) => ['id' => $p->id, 'label' => $p->getLabel()]));

function addItem() {
    const productId   = document.getElementById('sel_product').value;
    const productName = document.getElementById('sel_product').options[document.getElementById('sel_product').selectedIndex].text;
    const powerId     = document.getElementById('sel_power').value;
    const powerLabel  = document.getElementById('sel_power').options[document.getElementById('sel_power').selectedIndex].text;
    const qty         = parseInt(document.getElementById('sel_qty').value);
    const price       = parseFloat(document.getElementById('sel_price').value);

    if (!productId || !powerId || qty < 1 || price < 0) {
        alert('Please fill all item fields correctly.');
        return;
    }

    itemCounter++;
    const total = qty * price;
    items.push({ id: itemCounter, productId, productName, powerId, powerLabel, qty, price, total });

    renderItems();
    calculateTotals();

    // Reset selects
    document.getElementById('sel_qty').value = 1;
    document.getElementById('sel_price').value = 0;
}

function removeItem(id) {
    items = items.filter(i => i.id !== id);
    renderItems();
    calculateTotals();
}

function renderItems() {
    const tbody = document.getElementById('itemsBody');

    if (items.length === 0) {
        tbody.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-3 py-4 text-center text-gray-400">No items added yet</td></tr>';
        return;
    }

    let html = '';
    items.forEach((item, index) => {
        html += `
        <tr class="border-b">
            <td class="px-3 py-2 text-gray-400">${index + 1}</td>
            <td class="px-3 py-2">${item.productName}
                <input type="hidden" name="items[${index}][product_id]" value="${item.productId}">
            </td>
            <td class="px-3 py-2">${item.powerLabel}
                <input type="hidden" name="items[${index}][power_id]" value="${item.powerId}">
            </td>
            <td class="px-3 py-2 text-center">
                ${item.qty}
                <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            </td>
            <td class="px-3 py-2 text-right">
                Rs. ${item.price.toFixed(2)}
                <input type="hidden" name="items[${index}][unit_price]" value="${item.price}">
            </td>
            <td class="px-3 py-2 text-right font-medium">Rs. ${item.total.toFixed(2)}</td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeItem(${item.id})"
                    class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html;
}

function calculateTotals() {
    const gross    = items.reduce((sum, i) => sum + i.total, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax      = parseFloat(document.getElementById('tax').value) || 0;
    const net      = gross - discount + tax;

    document.getElementById('grossTotal').textContent = 'Rs. ' + gross.toFixed(2);
    document.getElementById('netTotal').textContent   = 'Rs. ' + net.toFixed(2);

    calculateBalance();
}

function calculateBalance() {
    const net  = parseFloat(document.getElementById('netTotal').textContent.replace('Rs. ', '')) || 0;
    const paid = parseFloat(document.getElementById('paid').value) || 0;
    const bal  = net - paid;

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