{{-- Shared form partial for create & edit --}}
{{-- Customer Selector --}}
@php $customers = \App\Models\Customer::where('user_id', auth()->id())->orderBy('name')->get(); @endphp
@if($customers->count())
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <label style="margin:0;white-space:nowrap;font-weight:600">Pilih Customer:</label>
            <select id="customer-select" style="flex:1;min-width:220px" onchange="fillCustomer(this)">
                <option value="">— Input manual —</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}"
                    data-company="{{ $c->company }}"
                    data-name="{{ $c->name }}"
                    data-address="{{ $c->address }}"
                    data-city="{{ $c->city }}"
                    data-province="{{ $c->province }}"
                    data-postal="{{ $c->postal_code }}"
                    data-country="{{ $c->country }}"
                    {{ old('customer_id', $invoice->customer_id ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->display_name }}
                </option>
                @endforeach
            </select>
            <a href="{{ route('customers.create') }}" class="btn btn-ghost btn-sm" target="_blank">+ Customer Baru</a>
        </div>
        <input type="hidden" name="customer_id" id="customer-id-input" value="{{ old('customer_id', $invoice->customer_id ?? '') }}">
    </div>
</div>
@endif
<div style="display:grid;grid-template-columns:1fr 1fr;gap:28px">

    {{-- LEFT: Invoice Info + Invoiced To --}}
    <div>
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span style="font-weight:600">Info Invoice</span></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="unpaid" {{ old('status', $invoice->status ?? 'unpaid') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="pending" {{ old('status', $invoice->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('status', $invoice->status ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Currency</label>
                        <select name="currency">
                            @foreach(\App\Models\Invoice::currencies() as $code => $label)
                            <option value="{{ $code }}" {{ old('currency', $invoice->currency ?? 'IDR') === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Invoice</label>
                        <input type="datetime-local" name="invoice_date"
                            value="{{ old('invoice_date', isset($invoice) ? $invoice->invoice_date?->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="form-group">
                        <label>Jatuh Tempo (Due Date)</label>
                        <input type="date" name="due_date"
                            value="{{ old('due_date', isset($invoice->due_date) ? $invoice->due_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-weight:600">Invoiced To (Klien)</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Perusahaan <span style="color:#a0aec0;font-weight:400">(opsional)</span></label>
                    <input type="text" name="client_company" value="{{ old('client_company', $invoice->client_company ?? '') }}" placeholder="PT. Contoh">
                </div>
                <div class="form-group">
                    <label>Nama Kontak <span style="color:#e53e3e">*</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name', $invoice->client_name ?? '') }}" required placeholder="Muhammad Royyan">
                </div>
                <div class="form-group">
                    <label>Alamat <span style="color:#e53e3e">*</span></label>
                    <textarea name="client_address" required placeholder="Jl. Contoh No. 1, Dusun ...">{{ old('client_address', $invoice->client_address ?? '') }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kota</label>
                        <input type="text" name="client_city" value="{{ old('client_city', $invoice->client_city ?? '') }}" placeholder="Malang">
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="client_province" value="{{ old('client_province', $invoice->client_province ?? '') }}" placeholder="Jawa Timur">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kode Pos</label>
                        <input type="text" name="client_postal_code" value="{{ old('client_postal_code', $invoice->client_postal_code ?? '') }}" placeholder="65162">
                    </div>
                    <div class="form-group">
                        <label>Negara</label>
                        <input type="text" name="client_country" value="{{ old('client_country', $invoice->client_country ?? 'Indonesia') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Pay To --}}
    <div>
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span style="font-weight:600">Pay To (Perusahaan Anda)</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Perusahaan <span style="color:#e53e3e">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name', $invoice->company_name ?? '') }}" required placeholder="PT Deneva">
                </div>
                <div class="form-group">
                    <label>Alamat <span style="color:#e53e3e">*</span></label>
                    <textarea name="company_address" required placeholder="Jl. Magelang, Yogyakarta">{{ old('company_address', $invoice->company_address ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label>NPWP <span style="color:#a0aec0;font-weight:400">(opsional)</span></label>
                    <input type="text" name="company_npwp" value="{{ old('company_npwp', $invoice->company_npwp ?? '') }}" placeholder="80.820.685.8-542.000">
                </div>
            </div>
        </div>

        {{-- Transactions --}}
        <div class="card">
            <div class="card-header">
                <span style="font-weight:600">Transaksi Pembayaran</span>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addTransaction()">+ Tambah</button>
            </div>
            <div class="card-body" id="transactions-wrap" style="padding-top:12px">
                @php $trxs = old('transactions', isset($invoice) ? $invoice->transactions->toArray() : []) @endphp
                @forelse($trxs as $i => $trx)
                <div class="trx-row" style="border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;position:relative">
                    <button type="button" onclick="this.closest('.trx-row').remove()" style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;color:#a0aec0;font-size:18px">&times;</button>
                    <div class="form-row" style="margin-bottom:10px">
                        <div class="form-group" style="margin-bottom:0">
                            <label style="font-size:12px">Tanggal</label>
                            <input type="date" name="transactions[{{ $i }}][transaction_date]"
                                value="{{ old("transactions.$i.transaction_date", isset($trx['transaction_date']) ? \Carbon\Carbon::parse($trx['transaction_date'])->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label style="font-size:12px">Gateway</label>
                            <input type="text" name="transactions[{{ $i }}][gateway]" value="{{ old("transactions.$i.gateway", $trx['gateway'] ?? '') }}" placeholder="QRIS / Transfer">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:10px">
                        <label style="font-size:12px">Transaction ID</label>
                        <input type="text" name="transactions[{{ $i }}][transaction_id]" value="{{ old("transactions.$i.transaction_id", $trx['transaction_id'] ?? '') }}" placeholder="8f615719-...">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label style="font-size:12px">Jumlah (Rp)</label>
                        <input type="number" name="transactions[{{ $i }}][amount]" value="{{ old("transactions.$i.amount", $trx['amount'] ?? '') }}" placeholder="447764">
                    </div>
                </div>
                @empty
                <p style="color:#a0aec0;font-size:13px;text-align:center;padding:12px 0">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Invoice Items --}}
<div class="card" style="margin-top:24px">
    <div class="card-header">
        <span style="font-weight:600">Item Invoice</span>
        <button type="button" class="btn btn-ghost btn-sm" onclick="addItem()">+ Tambah Item</button>
    </div>
    <div class="card-body">
        <table style="width:100%;border-collapse:collapse" id="items-table">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px 12px;background:#f7fafc;font-size:12px;color:#718096;border-bottom:1px solid #e2e8f0">Deskripsi</th>
                    <th style="text-align:right;padding:8px 12px;background:#f7fafc;font-size:12px;color:#718096;border-bottom:1px solid #e2e8f0;width:160px">Jumlah (Rp)</th>
                    <th style="text-align:center;padding:8px 12px;background:#f7fafc;font-size:12px;color:#718096;border-bottom:1px solid #e2e8f0;width:80px">Kena PPN</th>
                    <th style="width:40px;background:#f7fafc;border-bottom:1px solid #e2e8f0"></th>
                </tr>
            </thead>
            <tbody id="items-body">
                @php $items = old('items', isset($invoice) ? $invoice->items->toArray() : [['description'=>'','amount'=>'','is_taxed'=>false]]) @endphp
                @foreach($items as $i => $item)
                <tr class="item-row">
                    <td style="padding:8px 12px">
                        <input type="text" name="items[{{ $i }}][description]" value="{{ old("items.$i.description", $item['description'] ?? '') }}" required placeholder="Domain Registration - example.id - 1 Year/s">
                    </td>
                    <td style="padding:8px 12px">
                        <input type="number" name="items[{{ $i }}][amount]" value="{{ old("items.$i.amount", $item['amount'] ?? '') }}" required placeholder="420000" style="text-align:right" step="0.01" min="0">
                    </td>
                    <td style="padding:8px 12px;text-align:center">
                        <input type="checkbox" name="items[{{ $i }}][is_taxed]" {{ !empty($item['is_taxed']) ? 'checked' : '' }} style="width:auto">
                    </td>
                    <td style="padding:8px 12px;text-align:center">
                        <button type="button" onclick="removeItem(this)" style="background:none;border:none;cursor:pointer;color:#e53e3e;font-size:18px;line-height:1">&times;</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:12px;color:#718096;margin-top:12px">* Item yang dicentang "Kena PPN" akan dikenakan PPN 11%</p>
    </div>
</div>

<div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end">
    <a href="{{ route('invoices.index') }}" class="btn btn-ghost">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ isset($invoice) ? 'Simpan Perubahan' : 'Buat Invoice' }}
    </button>
</div>

@push('scripts')
<script>
let itemIdx = {{ count($items ?? []) }};
let trxIdx  = {{ count($trxs ?? []) }};

function fillCustomer(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('customer-id-input').value = opt.value;
    if (!opt.value) return;
    document.querySelector('[name=client_company]').value     = opt.dataset.company  || '';
    document.querySelector('[name=client_name]').value        = opt.dataset.name     || '';
    document.querySelector('[name=client_address]').value     = opt.dataset.address  || '';
    document.querySelector('[name=client_city]').value        = opt.dataset.city     || '';
    document.querySelector('[name=client_province]').value    = opt.dataset.province || '';
    document.querySelector('[name=client_postal_code]').value = opt.dataset.postal   || '';
    document.querySelector('[name=client_country]').value     = opt.dataset.country  || 'Indonesia';
}

// Auto-fill jika customer sudah dipilih (dari halaman customer)
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('customer-select');
    if (sel && sel.value) fillCustomer(sel);
});

function addItem() {
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td style="padding:8px 12px"><input type="text" name="items[${itemIdx}][description]" required placeholder="Deskripsi item"></td>
        <td style="padding:8px 12px"><input type="number" name="items[${itemIdx}][amount]" required placeholder="0" style="text-align:right" step="0.01" min="0"></td>
        <td style="padding:8px 12px;text-align:center"><input type="checkbox" name="items[${itemIdx}][is_taxed]" style="width:auto"></td>
        <td style="padding:8px 12px;text-align:center"><button type="button" onclick="removeItem(this)" style="background:none;border:none;cursor:pointer;color:#e53e3e;font-size:18px">&times;</button></td>
    `;
    tbody.appendChild(row);
    itemIdx++;
}

function removeItem(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { alert('Minimal 1 item diperlukan.'); return; }
    btn.closest('tr').remove();
}

function addTransaction() {
    const wrap = document.getElementById('transactions-wrap');
    const p = wrap.querySelector('p');
    if (p) p.remove();
    const div = document.createElement('div');
    div.className = 'trx-row';
    div.style.cssText = 'border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;position:relative';
    div.innerHTML = `
        <button type="button" onclick="this.closest('.trx-row').remove()" style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;color:#a0aec0;font-size:18px">&times;</button>
        <div class="form-row" style="margin-bottom:10px">
            <div class="form-group" style="margin-bottom:0"><label style="font-size:12px">Tanggal</label><input type="date" name="transactions[${trxIdx}][transaction_date]" value="{{ now()->format('Y-m-d') }}"></div>
            <div class="form-group" style="margin-bottom:0"><label style="font-size:12px">Gateway</label><input type="text" name="transactions[${trxIdx}][gateway]" placeholder="QRIS / Transfer"></div>
        </div>
        <div class="form-group" style="margin-bottom:10px"><label style="font-size:12px">Transaction ID</label><input type="text" name="transactions[${trxIdx}][transaction_id]" placeholder="8f615719-..."></div>
        <div class="form-group" style="margin-bottom:0"><label style="font-size:12px">Jumlah (Rp)</label><input type="number" name="transactions[${trxIdx}][amount]" placeholder="0"></div>
    `;
    wrap.appendChild(div);
    trxIdx++;
}
</script>
@endpush
