@extends('layouts.app')
@section('title', 'Return Purchase')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">↩️ Return / Reverse Purchase</h1>
            <p class="text-sm text-gray-500">{{ $purchase->invoice_no }} • {{ $purchase->supplier->name }}</p>
        </div>
        <a href="/purchases/{{ $purchase->id }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
            ← Back
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($returnableItems->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg p-4 text-sm">
            All items on this purchase have already been returned. Nothing left to reverse.
        </div>
    @else

        <form method="POST" action="/purchases/{{ $purchase->id }}/return" id="returnForm">
            @csrf

            {{-- Return Type Toggle --}}
            <div class="bg-white rounded-xl shadow p-5 mb-6">
                <h2 class="font-semibold text-gray-700 mb-3">Return Type</h2>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="return_type" value="full" id="type_full" checked
                               onchange="toggleReturnType()" class="w-4 h-4">
                        <span class="text-sm font-medium">Reverse Whole Purchase</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="return_type" value="partial" id="type_partial"
                               onchange="toggleReturnType()" class="w-4 h-4">
                        <span class="text-sm font-medium">Reverse Selected Articles</span>
                    </label>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    "Whole Purchase" returns every remaining unit on this invoice. "Selected Articles" lets you choose specific items and quantities.
                </p>
                @if($returnableItems->contains('stock_limited', true))
                    <div class="bg-orange-50 border border-orange-200 text-orange-700 rounded-lg p-3 mt-3 text-xs">
                        ⚠️ Some items on this purchase have already been partly sold, so stock is lower than what was purchased.
                        "Reverse Whole Purchase" will be blocked until you use "Reverse Selected Articles" to return only what's still in stock.
                    </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-xl shadow p-5 mb-6">
                <h2 class="font-semibold text-gray-700 mb-4">Items</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-blue-800 text-white">
                            <th class="px-3 py-2 text-left select-col" style="display:none;">Select</th>
                            <th class="px-3 py-2 text-left">Product</th>
                            <th class="px-3 py-2 text-left">Power</th>
                            <th class="px-3 py-2 text-center">Purchased</th>
                            <th class="px-3 py-2 text-center">Already Returned</th>
                            <th class="px-3 py-2 text-center">In Stock Now</th>
                            <th class="px-3 py-2 text-center">Available to Return</th>
                            <th class="px-3 py-2 text-center qty-col" style="display:none;">Return Qty</th>
                            <th class="px-3 py-2 text-right">Unit Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnableItems as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2 select-col" style="display:none;">
                                    <input type="checkbox" class="item-checkbox" data-id="{{ $item->id }}">
                                </td>
                                <td class="px-3 py-2 font-medium">{{ $item->product->name }}</td>
                                <td class="px-3 py-2">{{ $item->power->getLabel() }}</td>
                                <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                                <td class="px-3 py-2 text-center text-gray-400">{{ $item->returned_quantity }}</td>
                                <td class="px-3 py-2 text-center text-gray-500">
                                    {{ $item->current_stock }}
                                </td>
                                <td class="px-3 py-2 text-center font-medium {{ $item->stock_limited ? 'text-orange-600' : 'text-blue-700' }}">
                                    {{ $item->max_returnable }}
                                    @if($item->stock_limited)
                                        <span class="block text-xs text-orange-500 font-normal">⚠️ {{ $item->returnable_quantity - $item->max_returnable }} already sold</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center qty-col" style="display:none;">
                                    <input type="number" min="1" max="{{ $item->max_returnable }}"
                                           class="qty-input w-20 border border-gray-200 rounded px-2 py-1 text-center"
                                           data-id="{{ $item->id }}" data-max="{{ $item->max_returnable }}" disabled
                                           placeholder="0">
                                </td>
                                <td class="px-3 py-2 text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Refund Mode --}}
            <div class="bg-white rounded-xl shadow p-5 mb-6">
                <h2 class="font-semibold text-gray-700 mb-3">Refund Mode</h2>
                <div class="grid grid-cols-2 gap-4 max-w-sm">
                    <select name="refund_mode" required
                            class="col-span-2 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Select Refund Mode --</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-xs text-gray-500 mb-1">Notes (optional)</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                              placeholder="Reason for return, condition of items, etc."></textarea>
                </div>
            </div>

            <div id="hiddenItemsContainer"></div>

            <div class="flex justify-end gap-3">
                <a href="/purchases/{{ $purchase->id }}" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-red-600 text-white px-5 py-2.5 rounded-lg text-sm hover:bg-red-700 font-medium">
                    ↩️ Confirm Return
                </button>
            </div>
        </form>
    @endif

    <script>
        function toggleReturnType() {
            const isPartial = document.getElementById('type_partial').checked;

            document.querySelectorAll('.select-col, .qty-col').forEach(el => {
                el.style.display = isPartial ? '' : 'none';
            });

            document.querySelectorAll('.qty-input').forEach(input => {
                input.disabled = !isPartial;
                if (!isPartial) input.value = '';
            });

            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = false;
            });
        }

        // Enable qty input only when its row's checkbox is checked
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                const qtyInput = document.querySelector(`.qty-input[data-id="${this.dataset.id}"]`);
                qtyInput.disabled = !this.checked;
                if (!this.checked) qtyInput.value = '';
            });
        });

        // Clamp quantity input to its max returnable value
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', function () {
                const max = parseInt(this.dataset.max, 10);
                if (parseInt(this.value, 10) > max) {
                    this.value = max;
                }
            });
        });

        // Before submit, build hidden inputs for selected partial items
        document.getElementById('returnForm').addEventListener('submit', function (e) {
            const isPartial = document.getElementById('type_partial').checked;
            const container = document.getElementById('hiddenItemsContainer');
            container.innerHTML = '';

            if (!isPartial) return; // full return needs no item list

            let index = 0;
            let hasAtLeastOne = false;

            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                const id = cb.dataset.id;
                const qtyInput = document.querySelector(`.qty-input[data-id="${id}"]`);
                const qty = parseInt(qtyInput.value, 10);

                if (!qty || qty < 1) return;

                hasAtLeastOne = true;

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `items[${index}][purchase_item_id]`;
                idInput.value = id;

                const qtyHidden = document.createElement('input');
                qtyHidden.type = 'hidden';
                qtyHidden.name = `items[${index}][return_quantity]`;
                qtyHidden.value = qty;

                container.appendChild(idInput);
                container.appendChild(qtyHidden);
                index++;
            });

            if (!hasAtLeastOne) {
                e.preventDefault();
                alert('Please select at least one item and enter a valid return quantity.');
            }
        });
    </script>

@endsection