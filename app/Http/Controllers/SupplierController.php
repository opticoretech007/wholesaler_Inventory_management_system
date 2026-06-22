<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->latest()->paginate(20)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'city'            => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        Supplier::create([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'city'            => $request->city,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'notes'           => $request->notes,
        ]);

        return redirect('/suppliers')->with('success', '✅ Supplier added successfully!');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'city'            => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        $oldOpening = $supplier->opening_balance;
        $newOpening = $request->opening_balance ?? 0;
        $difference = $newOpening - $oldOpening;

        $supplier->update([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'city'            => $request->city,
            'notes'           => $request->notes,
            'is_active'       => $request->has('is_active'),
            'opening_balance' => $newOpening,
            'current_balance' => $supplier->current_balance + $difference,
        ]);

        return redirect('/suppliers')->with('success', '✅ Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', '🗑️ Supplier deleted.');
    }

    public function show(Supplier $supplier)
    {
        $purchases = $supplier->purchases()->latest()->paginate(10);
        $totalPurchases = $supplier->purchases()->sum('net_total');
        $totalPaid = $supplier->purchases()->sum('paid');
        $balance = $totalPurchases - $totalPaid;

        return view('suppliers.show', compact(
            'supplier', 'purchases', 'totalPurchases', 'totalPaid', 'balance'
        ));
    }
}