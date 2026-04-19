<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::where('user_id', auth()->id())->latest();

        if ($request->search) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('client_name', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(10)->withQueryString();

        $stats = [
            'total'   => Invoice::where('user_id', auth()->id())->count(),
            'paid'    => Invoice::where('user_id', auth()->id())->where('status', 'paid')->count(),
            'unpaid'  => Invoice::where('user_id', auth()->id())->where('status', 'unpaid')->count(),
            'pending' => Invoice::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'revenue' => Invoice::where('user_id', auth()->id())->where('status', 'paid')->sum('total'),
            'overdue' => Invoice::where('user_id', auth()->id())->whereNotIn('status', ['paid'])->whereNotNull('due_date')->whereDate('due_date', '<', now())->count(),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $invoice = new \App\Models\Invoice;

        // Pre-fill dari customer jika ada query param
        if ($customerId = request('customer_id')) {
            $customer = \App\Models\Customer::where('user_id', auth()->id())->find($customerId);
            if ($customer) {
                $invoice->customer_id     = $customer->id;
                $invoice->client_company  = $customer->company;
                $invoice->client_name     = $customer->name;
                $invoice->client_address  = $customer->address;
                $invoice->client_city     = $customer->city;
                $invoice->client_province = $customer->province;
                $invoice->client_postal_code = $customer->postal_code;
                $invoice->client_country  = $customer->country;
            }
        }

        return view('invoices.create', compact('invoice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name'    => 'required|string|max:255',
            'client_address' => 'required|string',
            'company_name'   => 'required|string|max:255',
            'company_address'=> 'required|string',
            'items'          => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount'      => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'user_id'        => auth()->id(),
            'customer_id'    => $request->customer_id ?: null,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'status'         => $request->status ?? 'unpaid',
            'invoice_date'   => $request->invoice_date ?? now(),
            'due_date'       => $request->due_date ?: null,
            'currency'       => $request->currency ?? 'IDR',
            'client_company' => $request->client_company,
            'client_name'    => $request->client_name,
            'client_address' => $request->client_address,
            'client_city'    => $request->client_city,
            'client_province'=> $request->client_province,
            'client_postal_code' => $request->client_postal_code,
            'client_country' => $request->client_country ?? 'Indonesia',
            'company_name'   => $request->company_name,
            'company_address'=> $request->company_address,
            'company_npwp'   => $request->company_npwp,
        ]);

        foreach ($request->items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'amount'      => $item['amount'],
                'is_taxed'    => isset($item['is_taxed']),
            ]);
        }

        $invoice->recalculate();

        // Save transactions if any
        if ($request->has('transactions')) {
            foreach ($request->transactions as $trx) {
                if (!empty($trx['transaction_id'])) {
                    $invoice->transactions()->create([
                        'transaction_date' => $trx['transaction_date'] ?? now(),
                        'gateway'          => $trx['gateway'],
                        'transaction_id'   => $trx['transaction_id'],
                        'amount'           => $trx['amount'],
                    ]);
                }
            }
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil dibuat!');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('items', 'transactions');
        $balance = $invoice->total - $invoice->transactions->sum('amount');
        return view('invoices.show', compact('invoice', 'balance'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $invoice->load('items', 'transactions');
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'client_name'    => 'required|string|max:255',
            'client_address' => 'required|string',
            'company_name'   => 'required|string|max:255',
            'company_address'=> 'required|string',
            'items'          => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount'      => 'required|numeric|min:0',
        ]);

        $invoice->update([
            'customer_id'    => $request->customer_id ?: null,
            'status'         => $request->status,
            'invoice_date'   => $request->invoice_date,
            'due_date'       => $request->due_date ?: null,
            'currency'       => $request->currency ?? 'IDR',
            'client_company' => $request->client_company,
            'client_name'    => $request->client_name,
            'client_address' => $request->client_address,
            'client_city'    => $request->client_city,
            'client_province'=> $request->client_province,
            'client_postal_code' => $request->client_postal_code,
            'client_country' => $request->client_country ?? 'Indonesia',
            'company_name'   => $request->company_name,
            'company_address'=> $request->company_address,
            'company_npwp'   => $request->company_npwp,
        ]);

        $invoice->items()->delete();
        foreach ($request->items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'amount'      => $item['amount'],
                'is_taxed'    => isset($item['is_taxed']),
            ]);
        }

        $invoice->recalculate();

        $invoice->transactions()->delete();
        if ($request->has('transactions')) {
            foreach ($request->transactions as $trx) {
                if (!empty($trx['transaction_id'])) {
                    $invoice->transactions()->create([
                        'transaction_date' => $trx['transaction_date'] ?? now(),
                        'gateway'          => $trx['gateway'],
                        'transaction_id'   => $trx['transaction_id'],
                        'amount'           => $trx['amount'],
                    ]);
                }
            }
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice berhasil diperbarui!');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus!');
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('items', 'transactions');
        $balance = $invoice->total - $invoice->transactions->sum('amount');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'balance'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $request->validate(['status' => 'required|in:paid,unpaid,pending']);
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status invoice diperbarui!');
    }
}
