<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->paginate(20)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'city'            => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        $customer = Customer::create([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'city'            => $request->city,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'notes'           => $request->notes,
        ]);

        return redirect('/customers')->with('success', '✅ Customer added successfully!');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
{
    $request->validate([
        'name'            => 'required|string|max:255',
        'phone'           => 'nullable|string|max:20',
        'city'            => 'nullable|string|max:100',
        'opening_balance' => 'nullable|numeric',
    ]);

    // Current balance ko difference se update karo
    $oldOpening = $customer->opening_balance;
    $newOpening = $request->opening_balance ?? 0;
    $difference = $newOpening - $oldOpening;

    $customer->update([
        'name'            => $request->name,
        'phone'           => $request->phone,
        'address'         => $request->address,
        'city'            => $request->city,
        'notes'           => $request->notes,
        'is_active'       => $request->has('is_active'),
        'opening_balance' => $newOpening,
        'current_balance' => $customer->current_balance + $difference,
    ]);

    return redirect('/customers')->with('success', '✅ Customer updated successfully!');
}

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', '🗑️ Customer deleted.');
    }

    public function show(Customer $customer)
    {
        $sales = $customer->sales()->with('items')->latest()->paginate(10);
        $totalSales = $customer->sales()->sum('net_total');
        $totalPaid = $customer->sales()->sum('paid');
        $balance = $totalSales - $totalPaid;

        return view('customers.show', compact('customer', 'sales', 'totalSales', 'totalPaid', 'balance'));
    }
}