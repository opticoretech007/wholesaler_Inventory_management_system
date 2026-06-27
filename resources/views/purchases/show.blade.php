@extends('layouts.app')
@section('title', 'Purchase Detail')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                {{ $purchase->invoice_no }}
                @if($purchase->status === 'returned')
                    <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-1 rounded-full align-middle">Returned</span>
                @elseif($purchase->status === 'partially_returned')
                    <span class="text-xs font-medium bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full align-middle">Partially Returned</span>
                @endif
            </h1>
            <p class="text-sm text-gray-500">{{ $purchase->invoice_date }} • {{ $purchase->supplier->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="/purchases" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
                ← Back
            </a>
            @if($purchase->status !== 'returned')
                <a href="/purchases/{{ $purchase->id }}/return"
                    class="bg-orange-100 text-orange-700 hover:bg-orange-200 px-4 py-2 rounded-lg text-sm">
                    ↩️ Return
                </a>
            @endif
            @if($purchase->balance > 0)
                <button type="button" onclick="openPayModal()"
                    class="bg-green-100 text-green-700 hover:bg-green-200 px-4 py-2 rounded-lg text-sm font-medium">
                    💰 Pay Balance
                </button>
            @endif
            <form method="POST" action="/purchases/{{ $purchase->id }}"
                onsubmit="return confirm('Delete this purchase? Stock will be reversed!')">
                @csrf @method('DELETE')
                <button class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold text-gray-700">Rs. {{ number_format($purchase->gross_total, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Gross Total</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold text-blue-700">Rs. {{ number_format($purchase->net_total, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Net Total</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold text-green-700">Rs. {{ number_format($purchase->paid, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Paid</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xl font-bold {{ $purchase->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rs. {{ number_format($purchase->balance, 2) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Balance</p>
        </div>
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-gray-700 mb-4">Items ({{ $purchase->items->count() }})</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-blue-800 text-white">
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Power</th>
                    <th class="px-4 py-2 text-center">Qty</th>
                    <th class="px-4 py-2 text-center">Returned</th>
                    <th class="px-4 py-2 text-right">Unit Price</th>
                    <th class="px-4 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $i => $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $item->product->name }}</td>
                        <td class="px-4 py-2">{{ $item->power->getLabel() }}</td>
                        <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-center {{ $item->returned_quantity > 0 ? 'text-red-600 font-medium' : 'text-gray-300' }}">
                            {{ $item->returned_quantity }}
                        </td>
                        <td class="px-4 py-2 text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-2 text-right font-medium">Rs. {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 bg-gray-50">
                    <td colspan="6" class="px-4 py-2 text-right font-semibold">Net Total:</td>
                    <td class="px-4 py-2 text-right font-bold text-blue-700">
                        Rs. {{ number_format($purchase->net_total, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Payment Info --}}
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="font-semibold text-gray-700 mb-3">Payment Info</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
            <div><span class="text-gray-400">Mode:</span> <span
                    class="font-medium capitalize">{{ $purchase->payment_mode }}</span></div>
            <div><span class="text-gray-400">Discount:</span> <span class="font-medium">Rs.
                    {{ number_format($purchase->discount, 2) }}</span></div>
            <div><span class="text-gray-400">Tax:</span> <span class="font-medium">Rs.
                    {{ number_format($purchase->tax, 2) }}</span></div>
            @if($purchase->memo)
                <div class="col-span-2"><span class="text-gray-400">Memo:</span> <span
                        class="font-medium">{{ $purchase->memo }}</span></div>
            @endif
        </div>
    </div>

    {{-- Payment History --}}
    @if($purchase->payments->count() > 0)
        <div class="bg-white rounded-xl shadow p-4 mt-6">
            <h2 class="font-semibold text-gray-700 mb-3">Payment History ({{ $purchase->payments->count() }})</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Mode</th>
                        <th class="px-3 py-2 text-right">Amount</th>
                        <th class="px-3 py-2 text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->payments as $payment)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 capitalize">{{ str_replace('_', ' ', $payment->payment_mode) }}</td>
                            <td class="px-3 py-2 text-right text-green-600 font-medium">
                                Rs. {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-3 py-2 text-gray-500">{{ $payment->notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Return History --}}
    @if($purchase->returns->count() > 0)
        <div class="bg-white rounded-xl shadow p-4 mt-6">
            <h2 class="font-semibold text-gray-700 mb-3">Return History ({{ $purchase->returns->count() }})</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Type</th>
                        <th class="px-3 py-2 text-left">Refund Mode</th>
                        <th class="px-3 py-2 text-right">Refund Amount</th>
                        <th class="px-3 py-2 text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->returns as $return)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $return->created_at->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 capitalize">{{ $return->return_type }}</td>
                            <td class="px-3 py-2 capitalize">{{ str_replace('_', ' ', $return->refund_mode) }}</td>
                            <td class="px-3 py-2 text-right text-red-600 font-medium">
                                Rs. {{ number_format($return->refund_amount, 2) }}
                            </td>
                            <td class="px-3 py-2 text-gray-500">{{ $return->notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Pay Balance Modal --}}
    @if($purchase->balance > 0)
        <div id="payModal" class="fixed inset-0 z-50 hidden" onclick="closePayModalOutside(event)">
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="relative bg-white shadow-2xl rounded-2xl w-full max-w-md">
                    <form method="POST" action="/purchases/{{ $purchase->id }}/pay">
                        @csrf
                        <div class="p-5 border-b flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 text-lg">💰 Pay Balance</h3>
                            <button type="button" onclick="closePayModal()"
                                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="bg-gray-50 rounded-lg p-3 text-sm flex justify-between">
                                <span class="text-gray-500">Current Balance Due</span>
                                <span class="font-bold text-red-600">Rs. {{ number_format($purchase->balance, 2) }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount Received</label>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                    max="{{ $purchase->balance }}" required
                                    value="{{ number_format($purchase->balance, 2, '.', '') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <p class="text-xs text-gray-400 mt-1">Max: Rs. {{ number_format($purchase->balance, 2) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Mode</label>
                                <select name="payment_mode" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                                <textarea name="notes" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>

                        <div class="p-5 border-t flex gap-3">
                            <button type="button" onclick="closePayModal()"
                                class="flex-1 bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 rounded-lg text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg text-sm">
                                ✅ Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openPayModal() {
                document.getElementById('payModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closePayModal() {
                document.getElementById('payModal').classList.add('hidden');
                document.body.style.overflow = '';
            }
            function closePayModalOutside(e) {
                if (e.target === e.currentTarget) closePayModal();
            }
        </script>
    @endif

@endsection