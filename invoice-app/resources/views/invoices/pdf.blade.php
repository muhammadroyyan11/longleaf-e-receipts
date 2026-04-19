<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #2d3748; margin: 0; padding: 0; }
    .page { padding: 40px; }
    .logo { font-size: 26px; font-weight: 800; color: #1a202c; margin-bottom: 20px; }
    .logo-accent { color: #0099d8; }
    .inv-number { font-size: 13px; color: #4a5568; margin-bottom: 6px; }
    .inv-status { font-size: 20px; font-weight: 800; margin-bottom: 4px; }
    .status-paid { color: #38a169; }
    .status-unpaid { color: #e53e3e; }
    .status-pending { color: #d69e2e; }
    .inv-date { color: #0099d8; font-size: 12px; margin-bottom: 20px; }
    hr { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
    .parties { width: 100%; margin-bottom: 24px; }
    .parties td { vertical-align: top; width: 50%; padding-right: 20px; }
    .party-label { font-weight: 700; font-size: 12px; margin-bottom: 8px; }
    .party-info { font-size: 11px; line-height: 1.8; color: #4a5568; }
    .blue { color: #0099d8; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    table.items th { background: #f7fafc; padding: 8px 12px; text-align: left; font-size: 11px; color: #718096; border-bottom: 1px solid #e2e8f0; }
    table.items th.right { text-align: right; }
    table.items td { padding: 10px 12px; border-bottom: 1px solid #f0f4f8; font-size: 11px; }
    table.items td.right { text-align: right; font-weight: 500; }
    .total-row td { background: #f7fafc; font-weight: 700; font-size: 12px; }
    .subtotal-row td { color: #718096; font-size: 11px; }
    .tax-note { font-size: 10px; color: #718096; margin: 6px 0 16px; }
    table.trx { width: 100%; border-collapse: collapse; margin-top: 16px; }
    table.trx th { background: #f7fafc; padding: 8px 12px; font-size: 11px; color: #718096; border-bottom: 1px solid #e2e8f0; }
    table.trx td { padding: 8px 12px; font-size: 11px; border-bottom: 1px solid #f0f4f8; }
    .balance-row td { background: #f7fafc; font-weight: 700; }
</style>
</head>
<body>
<div class="page">
    @php
        $appLogo    = \App\Models\Setting::get('company_logo');
        $appName    = \App\Models\Setting::get('company_name', 'InvoiceGen');
        $appTagline = \App\Models\Setting::get('company_tagline');
    @endphp
    <div style="margin-bottom:20px">
        @if($appLogo)
            <img src="{{ public_path('storage/' . $appLogo) }}" alt="{{ $appName }}" style="max-height:50px;max-width:180px">
            @if($appTagline)
                <div style="font-size:10px;color:#718096;margin-top:3px">{{ $appTagline }}</div>
            @endif
        @else
            <div class="logo"><span class="logo-accent">&#9664;</span> {{ $appName }}</div>
            @if($appTagline)
                <div style="font-size:10px;color:#718096;margin-top:2px">{{ $appTagline }}</div>
            @endif
        @endif
    </div>

    <div class="inv-number">Invoice <strong>#{{ $invoice->invoice_number }}</strong></div>
    <div class="inv-status">STATUS: <span class="status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></div>
    @if($invoice->invoice_date)
    <div class="inv-date">Invoice Date {{ $invoice->status === 'paid' ? 'Paid' : '' }}: {{ $invoice->invoice_date->format('d/m/Y (H:i)') }}</div>
    @endif

    <hr>

    <table class="parties">
        <tr>
            <td>
                <div class="party-label">Invoiced To</div>
                <div class="party-info">
                    @if($invoice->client_company)<div class="blue">{{ $invoice->client_company }}</div>@endif
                    <div class="blue">{{ $invoice->client_name }}</div>
                    <div>{{ $invoice->client_address }}</div>
                    @if($invoice->client_city)<div>Kec {{ $invoice->client_city }}</div>@endif
                    @if($invoice->client_province)<div>{{ $invoice->client_province }}{{ $invoice->client_postal_code ? ', '.$invoice->client_postal_code : '' }}</div>@endif
                    <div>{{ $invoice->client_country }}</div>
                </div>
            </td>
            <td>
                <div class="party-label">Pay To</div>
                <div class="party-info">
                    <div>{{ $invoice->company_name }}</div>
                    <div>{{ $invoice->company_address }}</div>
                    @if($invoice->company_npwp)<div style="margin-top:6px">NPWP: <span class="blue">{{ $invoice->company_npwp }}</span></div>@endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="right" style="width:140px">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td><span class="blue">{{ $item->description }}</span>@if($item->is_taxed) <span style="color:#718096;font-size:10px"> *</span>@endif</td>
                <td class="right">{{ $invoice->formatMoney($item->amount) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td style="text-align:right">Sub Total</td>
                <td class="right">{{ $invoice->formatMoney($invoice->subtotal) }}</td>
            </tr>
            <tr class="subtotal-row">
                <td style="text-align:right">Taxable Base</td>
                <td class="right">{{ $invoice->formatMoney($invoice->taxable_base) }}</td>
            </tr>
            <tr class="subtotal-row">
                <td style="text-align:right">VAT (11%)</td>
                <td class="right">{{ $invoice->formatMoney($invoice->vat) }}</td>
            </tr>
            <tr class="total-row">
                <td style="text-align:right">Total</td>
                <td class="right">{{ $invoice->formatMoney($invoice->total) }}</td>
            </tr>
        </tbody>
    </table>

    @if($invoice->items->where('is_taxed', true)->count())
    <p class="tax-note">* Indicates a taxed item.</p>
    @endif

    @if($invoice->transactions->count())
    <table class="trx">
        <thead>
            <tr>
                <th>Transaction Date</th>
                <th>Gateway</th>
                <th>Transaction ID</th>
                <th style="text-align:right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->transactions as $trx)
            <tr>
                <td>{{ $trx->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $trx->gateway }}</td>
                <td style="font-family:monospace;font-size:10px">{{ $trx->transaction_id }}</td>
                <td style="text-align:right">{{ $invoice->formatMoney($trx->amount) }}</td>
            </tr>
            @endforeach
            <tr class="balance-row">
                <td colspan="3" style="text-align:right">Balance</td>
                <td style="text-align:right">{{ $invoice->formatMoney($balance) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @php
        $sig        = \App\Models\Setting::get('signature');
        $signerName = \App\Models\Setting::get('signer_name');
    @endphp
    @if($sig || $signerName)
    <div style="margin-top:36px;text-align:right">
        <div style="display:inline-block;text-align:center;min-width:160px">
            <div style="font-size:11px;color:#4a5568;margin-bottom:6px">Regards,</div>
            @if($sig)
            <img src="{{ $sig }}" alt="TTD" style="max-height:70px;max-width:180px;display:block;margin:0 auto 4px">
            @else
            <div style="height:70px"></div>
            @endif
            <div style="border-top:1px solid #2d3748;padding-top:5px;font-size:10px;color:#4a5568;font-weight:600">
                {{ $signerName ?? 'Penandatangan' }}
            </div>
        </div>
    </div>
    @endif
</div>
</body>
</html>
