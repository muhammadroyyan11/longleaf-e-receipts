@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Detail Invoice')

@section('content')
<div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap">
    <a href="{{ route('invoices.index') }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline btn-sm">Edit Invoice</a>
    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
    </a>
    {{-- Quick status update --}}
    <form method="POST" action="{{ route('invoices.status', $invoice) }}" style="display:flex;gap:6px;align-items:center">
        @csrf @method('PATCH')
        <select name="status" style="padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
            <option value="unpaid" {{ $invoice->status=='unpaid'?'selected':'' }}>Unpaid</option>
            <option value="pending" {{ $invoice->status=='pending'?'selected':'' }}>Pending</option>
            <option value="paid" {{ $invoice->status=='paid'?'selected':'' }}>Paid</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Update Status</button>
    </form>
</div>

<div class="card" style="max-width:860px">
    <div class="card-body" style="padding:40px">

        {{-- Header: Logo + Invoice Number --}}
        @php
            $appLogo    = \App\Models\Setting::get('company_logo');
            $appName    = \App\Models\Setting::get('company_name', 'InvoiceGen');
            $appTagline = \App\Models\Setting::get('company_tagline');
        @endphp
        <div style="margin-bottom:24px">
            <div style="margin-bottom:16px">
                @if($appLogo)
                    <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" style="max-height:56px;max-width:200px">
                    @if($appTagline)
                        <div style="font-size:12px;color:#718096;margin-top:4px">{{ $appTagline }}</div>
                    @endif
                @else
                    <div style="font-size:28px;font-weight:800;color:#1a202c">
                        <span style="color:#0099d8">&#9664;</span> {{ $appName }}
                    </div>
                    @if($appTagline)
                        <div style="font-size:12px;color:#718096;margin-top:2px">{{ $appTagline }}</div>
                    @endif
                @endif
            </div>
            <div style="color:#4a5568;font-size:14px;margin-bottom:8px">Invoice <strong style="color:#1a202c">#{{ $invoice->invoice_number }}</strong></div>
            <div style="font-size:22px;font-weight:800;margin-bottom:4px">
                STATUS: <span class="status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
            </div>
            @if($invoice->invoice_date)
            <div style="color:#0099d8;font-size:13px">
                Invoice Date {{ $invoice->status === 'paid' ? 'Paid' : '' }}: {{ $invoice->invoice_date->format('d/m/Y (H:i)') }}
            </div>
            @endif
            @if($invoice->due_date)
            <div style="font-size:13px;margin-top:4px;color:{{ $invoice->isOverdue() ? '#e53e3e' : '#718096' }};font-weight:{{ $invoice->isOverdue() ? '600' : '400' }}">
                Due Date: {{ $invoice->due_date->format('d/m/Y') }}
                @if($label = $invoice->dueDaysLabel())
                    — <span>{{ $label }}</span>
                @endif
            </div>
            @endif
        </div>

        <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0">

        {{-- Parties --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-bottom:28px">
            <div>
                <div style="font-weight:700;font-size:13px;color:#1a202c;margin-bottom:10px">Invoiced To</div>
                <div style="font-size:13px;line-height:1.8;color:#4a5568">
                    @if($invoice->client_company)
                        <div style="color:#0099d8">{{ $invoice->client_company }}</div>
                    @endif
                    <div style="color:#0099d8">{{ $invoice->client_name }}</div>
                    <div>{{ $invoice->client_address }}</div>
                    @if($invoice->client_city)
                        <div>Kec {{ $invoice->client_city }}</div>
                    @endif
                    @if($invoice->client_province || $invoice->client_postal_code)
                        <div>{{ $invoice->client_province }}{{ $invoice->client_postal_code ? ', ' . $invoice->client_postal_code : '' }}</div>
                    @endif
                    <div>{{ $invoice->client_country }}</div>
                </div>
            </div>
            <div>
                <div style="font-weight:700;font-size:13px;color:#1a202c;margin-bottom:10px">Pay To</div>
                <div style="font-size:13px;line-height:1.8;color:#4a5568">
                    <div>{{ $invoice->company_name }}</div>
                    <div>{{ $invoice->company_address }}</div>
                    @if($invoice->company_npwp)
                        <div style="margin-top:8px">NPWP: <span style="color:#0099d8">{{ $invoice->company_npwp }}</span></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invoice Items --}}
        <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:8px">
            <div style="background:#f7fafc;padding:12px 16px;font-weight:600;font-size:13px;border-bottom:1px solid #e2e8f0">Invoice Items</div>
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f7fafc">
                        <th style="padding:10px 16px;text-align:left;font-size:12px;color:#718096;font-weight:600;border-bottom:1px solid #e2e8f0">Description</th>
                        <th style="padding:10px 16px;text-align:right;font-size:12px;color:#718096;font-weight:600;border-bottom:1px solid #e2e8f0;width:160px">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td style="padding:12px 16px;border-bottom:1px solid #f0f4f8;font-size:13px">
                            <span style="color:#0099d8">{{ $item->description }}</span>
                            @if($item->is_taxed) <span style="color:#718096;font-size:11px"> *</span> @endif
                        </td>
                        <td style="padding:12px 16px;border-bottom:1px solid #f0f4f8;text-align:right;font-size:13px;font-weight:500">
                            {{ $invoice->formatMoney($item->amount) }}
                        </td>
                    </tr>
                    @endforeach
                    {{-- Totals --}}
                    <tr>
                        <td style="padding:10px 16px;text-align:right;font-size:13px;color:#718096;border-top:1px solid #e2e8f0">Sub Total</td>
                        <td style="padding:10px 16px;text-align:right;font-size:13px;font-weight:600;border-top:1px solid #e2e8f0">{{ $invoice->formatMoney($invoice->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 16px;text-align:right;font-size:13px;color:#718096">Taxable Base</td>
                        <td style="padding:8px 16px;text-align:right;font-size:13px;font-weight:600">{{ $invoice->formatMoney($invoice->taxable_base) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 16px;text-align:right;font-size:13px;color:#718096">VAT (11%)</td>
                        <td style="padding:8px 16px;text-align:right;font-size:13px;font-weight:600">{{ $invoice->formatMoney($invoice->vat) }}</td>
                    </tr>
                    <tr style="background:#f7fafc">
                        <td style="padding:12px 16px;text-align:right;font-size:14px;font-weight:700;border-top:1px solid #e2e8f0">Total</td>
                        <td style="padding:12px 16px;text-align:right;font-size:14px;font-weight:700;border-top:1px solid #e2e8f0">{{ $invoice->formatMoney($invoice->total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($invoice->items->where('is_taxed', true)->count())
        <p style="font-size:12px;color:#718096;margin-bottom:20px">* Indicates a taxed item.</p>
        @endif

        {{-- Transactions --}}
        @if($invoice->transactions->count())
        <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-top:20px">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f7fafc">
                        <th style="padding:10px 16px;text-align:left;font-size:12px;color:#718096;font-weight:600;border-bottom:1px solid #e2e8f0">Transaction Date</th>
                        <th style="padding:10px 16px;text-align:left;font-size:12px;color:#718096;font-weight:600;border-bottom:1px solid #e2e8f0">Gateway</th>
                        <th style="padding:10px 16px;text-align:left;font-size:12px;color:#718096;font-weight:600;border-bottom:1px solid #e2e8f0">Transaction ID</th>
                        <th style="padding:10px 16px;text-align:right;font-size:12px;color:#718096;font-weight:600;border-bottom:1px solid #e2e8f0">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->transactions as $trx)
                    <tr>
                        <td style="padding:12px 16px;font-size:13px;border-bottom:1px solid #f0f4f8">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                        <td style="padding:12px 16px;font-size:13px;border-bottom:1px solid #f0f4f8">{{ $trx->gateway }}</td>
                        <td style="padding:12px 16px;font-size:13px;color:#718096;border-bottom:1px solid #f0f4f8;font-family:monospace">{{ $trx->transaction_id }}</td>
                        <td style="padding:12px 16px;font-size:13px;text-align:right;font-weight:500;border-bottom:1px solid #f0f4f8">{{ $invoice->formatMoney($trx->amount) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background:#f7fafc">
                        <td colspan="3" style="padding:10px 16px;text-align:right;font-size:13px;font-weight:700;border-top:1px solid #e2e8f0">Balance</td>
                        <td style="padding:10px 16px;text-align:right;font-size:13px;font-weight:700;border-top:1px solid #e2e8f0">{{ $invoice->formatMoney($balance) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        {{-- Tanda Tangan --}}
        @php
            $sig        = \App\Models\Setting::get('signature');
            $signerName = \App\Models\Setting::get('signer_name');
        @endphp
        @if($sig || $signerName)
        <div style="margin-top:40px;display:flex;justify-content:flex-end">
            <div style="text-align:center;min-width:180px">
                <div style="font-size:13px;color:#4a5568;margin-bottom:8px">Regards,</div>
                @if($sig)
                <img src="{{ $sig }}" alt="TTD" style="max-height:80px;max-width:200px;display:block;margin:0 auto 4px">
                @else
                <div style="height:80px"></div>
                @endif
                <div style="border-top:1px solid #2d3748;padding-top:6px;font-size:12px;color:#4a5568;font-weight:500">
                    {{ $signerName ?? 'Penandatangan' }}
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
