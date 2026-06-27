<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Stock;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function create(Purchase $purchase)
    {
        $purchase->load('items.product', 'items.power', 'supplier');

        // Only items that still have returnable quantity (purchase-side)
        $returnableItems = $purchase->items->filter(function ($item) {
            return $item->returnable_quantity > 0;
        });

        // Attach the actual available stock for each item, since stock may
        // have been reduced by sales since this purchase — we can never
        // return more than what's physically still in stock.
        $returnableItems->each(function ($item) {
            $stock = Stock::where('product_id', $item->product_id)
                          ->where('power_id', $item->power_id)
                          ->first();

            $availableStock = $stock ? $stock->quantity : 0;
            $item->current_stock = $availableStock;
            $item->max_returnable = min($item->returnable_quantity, $availableStock);
            $item->stock_limited = $availableStock < $item->returnable_quantity;
        });

        return view('purchases.return', compact('purchase', 'returnableItems'));
    }

    public function store(Request $request, Purchase $purchase)
    {
        $request->validate([
            'return_type'  => 'required|in:full,partial',
            'refund_mode'  => 'required|in:cash,bank_transfer',
            'notes'        => 'nullable|string',
            'items'        => 'required_if:return_type,partial|array',
            'items.*.purchase_item_id' => 'required_with:items|exists:purchase_items,id',
            'items.*.return_quantity'  => 'required_with:items|integer|min:1',
        ]);

        if ($purchase->status === 'returned') {
            return back()->with('error', '⚠️ This purchase has already been fully returned.');
        }

        $purchase->load('items', 'supplier');

        // Build the list of (purchaseItem, quantityToReturn) pairs
        $linesToReturn = [];

        if ($request->return_type === 'full') {
            foreach ($purchase->items as $item) {
                if ($item->returnable_quantity > 0) {
                    $availableStock = $this->getAvailableStock($item);
                    $qty = min($item->returnable_quantity, $availableStock);

                    if ($qty < $item->returnable_quantity) {
                        return back()->withErrors([
                            'items' => "Cannot fully return {$item->product->name} — only {$availableStock} unit(s) currently in stock (some were sold). Please use 'Reverse Selected Articles' to return the available {$qty} unit(s), or restock before returning the rest."
                        ]);
                    }

                    if ($qty > 0) {
                        $linesToReturn[] = [
                            'item' => $item,
                            'qty'  => $qty,
                        ];
                    }
                }
            }
        } else {
            foreach ($request->items as $row) {
                $item = $purchase->items->firstWhere('id', $row['purchase_item_id']);

                if (!$item) {
                    continue;
                }

                $qty = (int) $row['return_quantity'];

                if ($qty > $item->returnable_quantity) {
                    return back()->withErrors([
                        'items' => "Cannot return {$qty} units of {$item->product->name} — only {$item->returnable_quantity} unit(s) remain returnable from this purchase."
                    ])->withInput();
                }

                $availableStock = $this->getAvailableStock($item);
                if ($qty > $availableStock) {
                    return back()->withErrors([
                        'items' => "Cannot return {$qty} units of {$item->product->name} — only {$availableStock} unit(s) currently in stock (the rest were already sold)."
                    ])->withInput();
                }

                if ($qty > 0) {
                    $linesToReturn[] = ['item' => $item, 'qty' => $qty];
                }
            }
        }

        if (empty($linesToReturn)) {
            return back()->with('error', '⚠️ No items selected to return.');
        }

        $refundAmount = 0;

        DB::transaction(function () use ($purchase, $linesToReturn, $request, &$refundAmount) {

            $purchaseReturn = PurchaseReturn::create([
                'purchase_id'  => $purchase->id,
                'return_type'  => $request->return_type,
                'refund_mode'  => $request->refund_mode,
                'refund_amount' => 0, // filled in after computing
                'notes'        => $request->notes,
                'returned_by'  => auth()->id(),
            ]);

            foreach ($linesToReturn as $line) {
                $item = $line['item'];
                $qty  = $line['qty'];
                $lineTotal = $qty * $item->unit_price;
                $refundAmount += $lineTotal;

                // Record the return line item
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_item_id'   => $item->id,
                    'product_id'         => $item->product_id,
                    'power_id'           => $item->power_id,
                    'returned_quantity'  => $qty,
                    'unit_price'         => $item->unit_price,
                    'total_amount'       => $lineTotal,
                ]);

                // Update purchase item's returned quantity
                $item->returned_quantity += $qty;
                $item->save();

                // Remove quantity from stock (lock row to avoid race conditions
                // with concurrent sales/purchases during this transaction)
                $stock = Stock::where('product_id', $item->product_id)
                              ->where('power_id', $item->power_id)
                              ->lockForUpdate()
                              ->first();

                if ($stock) {
                    if ($qty > $stock->quantity) {
                        throw new \RuntimeException(
                            "Stock for {$item->product->name} changed and is no longer sufficient for this return. Please try again."
                        );
                    }
                    $stock->quantity -= $qty;
                    $stock->save();
                }

                // Log stock transaction
                StockTransaction::create([
                    'product_id' => $item->product_id,
                    'power_id'   => $item->power_id,
                    'type'       => 'OUT',
                    'quantity'   => $qty,
                ]);
            }

            // Update refund amount on the return record
            $purchaseReturn->refund_amount = $refundAmount;
            $purchaseReturn->save();

            // The refund's effect on paid/balance depends on whether this
            // purchase was on credit when the return happens:
            //  - If there was an outstanding balance (credit), the returned
            //    goods simply reduce what we owe — 'paid' stays untouched,
            //    'balance' drops by the refund amount.
            //  - If the purchase was fully paid (balance == 0), the refund
            //    is a real cash/bank refund, so it comes back out of 'paid'.
            $hadOutstandingBalance = $purchase->balance > 0;

            if ($hadOutstandingBalance) {
                $purchase->balance = max(0, $purchase->balance - $refundAmount);
            } else {
                $purchase->paid = max(0, $purchase->paid - $refundAmount);
            }
            $purchase->net_total = max(0, $purchase->net_total - $refundAmount);

            // Determine new status based on a fresh read of item quantities
            // (returned_quantity was already saved per-item in the loop above)
            $itemsStillOwed = $purchase->items()->get()->every(function ($i) {
                return ($i->quantity - $i->returned_quantity) <= 0;
            });
            $purchase->status = $itemsStillOwed ? 'returned' : 'partially_returned';
            $purchase->save();

            // Sync supplier balance: if this purchase was on credit (had an
            // outstanding balance), the full refund amount reduces what we
            // owe the supplier — regardless of how it was split between
            // paid/balance above. If the purchase was fully paid, the refund
            // is a cash/bank refund only and doesn't touch supplier balance.
            if ($hadOutstandingBalance) {
                $supplier = $purchase->supplier;
                if ($supplier) {
                    $supplier->current_balance = max(0, $supplier->current_balance - $refundAmount);
                    $supplier->save();
                }
            }
        });

        return redirect("/purchases/{$purchase->id}")
            ->with('success', '↩️ Return processed successfully. Stock and balance updated.');
    }

    /**
     * Get the currently available stock quantity for a purchase item's
     * product+power combination. Stock may be lower than what was purchased
     * if some units have already been sold.
     */
    private function getAvailableStock($item): int
    {
        $stock = Stock::where('product_id', $item->product_id)
                      ->where('power_id', $item->power_id)
                      ->first();

        return $stock ? $stock->quantity : 0;
    }
}