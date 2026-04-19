@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Invoice')

@section('content')
{{-- Stats --}}
<div class="stat-grid">
    <div class="stat-card blue">
        <div class="stat-label">Total Invoice</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Lunas</div>
        <div class="stat-value">{{ $stats['paid'] }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">Belum Bayar</div>
        <div class="stat-value">{{ $stats['unpaid'] }}</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Pending</div>
        <div class="stat-value">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat-card blue" style="border-color:#6b46c1">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value small">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-color:#e53e3e;border-left-width:4px">
        <div class="stat-label">Overdue</div>
        <div class="stat-value" style="color:#e53e3e">{{ $stats['overdue'] }}</div>
    </div>
</div>

{{-- Filter & Search --}}
<div class="card">
    <div class="card-header">
        <span style="font-weight:600;font-size:15px">Daftar Invoice</span>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Invoice
        </a>
    </div>
    <div class="card-body" style="padding-bottom:0">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor / nama klien..." style="flex:1;min-width:200px">
            <select name="status" style="width:160px">
                <option value="">Semua Status</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="unpaid" {{ request('status')=='unpaid'?'selected':'' }}>Unpaid</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('invoices.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Klien</th>
                    <th>Tanggal</th>
                    <th>Jatuh Tempo</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td style="font-weight:600;color:#0099d8">{{ $inv->invoice_number }}</td>
                    <td>
                        <div style="font-weight:500">{{ $inv->client_name }}</div>
                        @if($inv->client_company)
                            <div style="font-size:12px;color:#718096">{{ $inv->client_company }}</div>
                        @endif
                    </td>
                    <td style="color:#718096;font-size:13px">
                        {{ $inv->invoice_date ? $inv->invoice_date->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="font-size:13px">
                        @if($inv->due_date)
                            <div style="color:{{ $inv->isOverdue() ? '#e53e3e' : ($inv->due_date->isToday() ? '#d69e2e' : '#4a5568') }};font-weight:{{ $inv->isOverdue() ? '600' : '400' }}">
                                {{ $inv->due_date->format('d/m/Y') }}
                            </div>
                            @if($label = $inv->dueDaysLabel())
                                <div style="font-size:11px;color:{{ $inv->isOverdue() ? '#e53e3e' : '#718096' }}">{{ $label }}</div>
                            @endif
                        @else
                            <span style="color:#a0aec0">—</span>
                        @endif
                    </td>
                    <td style="font-weight:600">
                        Rp {{ number_format($inv->total, 0, ',', '.') }}
                        @if($inv->currency !== 'IDR')
                            <span style="font-size:11px;color:#718096;font-weight:400">({{ $inv->currency }})</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $inv->status }}">{{ strtoupper($inv->status) }}</span>
                    </td>
                    <td style="text-align:right">
                        <div style="display:flex;gap:6px;justify-content:flex-end">
                            <a href="{{ route('invoices.show', $inv) }}" class="btn btn-ghost btn-sm">Lihat</a>
                            <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-outline btn-sm">Edit</a>
                            <a href="{{ route('invoices.pdf', $inv) }}" class="btn btn-ghost btn-sm" title="Download PDF">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                PDF
                            </a>
                            <form method="POST" action="{{ route('invoices.destroy', $inv) }}" onsubmit="return confirm('Hapus invoice ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#a0aec0">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;opacity:.4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Belum ada invoice. <a href="{{ route('invoices.create') }}" style="color:#0099d8">Buat sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
    <div style="padding:16px 24px;border-top:1px solid #f0f4f8">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection
