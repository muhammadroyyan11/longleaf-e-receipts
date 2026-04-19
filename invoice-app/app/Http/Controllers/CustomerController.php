<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::where('user_id', auth()->id())
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('company', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->withCount('invoices')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'company'     => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'required|string',
            'city'        => 'nullable|string|max:100',
            'province'    => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
        ]);

        $customer = Customer::create(['user_id' => auth()->id()] + $data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer berhasil ditambahkan!');
    }

    public function show(Customer $customer)
    {
        abort_unless($customer->user_id === auth()->id(), 403);
        $customer->load(['invoices' => fn($q) => $q->latest()->limit(10)]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        abort_unless($customer->user_id === auth()->id(), 403);
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_unless($customer->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'company'     => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'required|string',
            'city'        => 'nullable|string|max:100',
            'province'    => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
        ]);

        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        abort_unless($customer->user_id === auth()->id(), 403);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus!');
    }
}
