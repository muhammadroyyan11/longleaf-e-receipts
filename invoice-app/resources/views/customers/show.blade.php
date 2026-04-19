@extends('layouts.app')
@section('title', $customer->name)
@section('page-title', 'Detail Customer')

@section('content')
<div style="display:flex;gap:12px;margin-bottom:24px">
    <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline btn-sm">Edit</a>
    <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm">+ Buat Invoice</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">
    <div class="card">
        <div class="card-header"><span style="font-weight:600">Info Customer</span></div>
        <div class="card-body" style="font-size:14px;line-height:2">
            <div><span style="color:#718096;width:110px;display:inline-block">Nama</span> <strong>{{ $customer->name }}</strong></div>
            @if($customer->company)
            <div><span style="color:#718096;width:110px;display:inline-block">Perusahaan</span> {{ $customer->company }}</div>
            @endif
            @if($customer->email)
            <div><span style="color:#718096;width:110px;display:inline-block">Email</span> <a href="mailto:{{ $customer->email }}" style="color:#0099d8">{{ $customer->email }}</a></div>
            @endif
            @if($customer->phone)
            <div><span style="color:#718096;width:110px;display:inline-block">Telepon</span> {{ $customer->phone }}</div>
            @endif
            <div><span style="color:#718096;width:110px;display:inline-block">Alamat</span> {{ $customer->address }}</div>
            @if($customer->city)
            <div><span style="color:#718096;width:110px;display:inline-block">Kota</span> {{ $customer->city }}{{ $customer->province ? ', '.$customer->province : '' }}</div>
            @endif
            <div><span style="color:#718096;width:110px;display:inline-block">Negara</span> {{ $customer->country }}</div>
            @if($customer->notes)
            <div style="margin-top:12px;padding:12px;background:#f7fafc;border-radius:6px;font-size:13px;color:#4a5568">
                <strong>Catatan:</strong> {{ $customer->notes }}
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-weight:600">Riwayat Invoice</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->invoices as $inv)
                    <tr>
                        <td><a href="{{ route('invoices.show', $inv) }}" style="color:#0099d8;font-weight:600">{{ $inv->invoice_number }}</a></td>
                        <td style="font-size:12px;color:#718096">{{ $inv->invoice_date?->format('d/m/Y') ?? '-' }}</td>
                        <td style="font-size:13px;font-weight:500">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $inv->status }}">{{ strtoupper($inv->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#a0aec0;padding:20px">Belum ada invoice</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
