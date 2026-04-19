@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'CRM — Daftar Customer')

@section('content')
<div class="card">
    <div class="card-header">
        <span style="font-weight:600;font-size:15px">Customers ({{ $customers->total() }})</span>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Customer
        </a>
    </div>
    <div class="card-body" style="padding-bottom:0">
        <form method="GET" style="display:flex;gap:12px;margin-bottom:20px">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, perusahaan, email..." style="flex:1">
            <button type="submit" class="btn btn-outline btn-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama / Perusahaan</th>
                    <th>Kontak</th>
                    <th>Kota</th>
                    <th>Invoice</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $c->name }}</div>
                        @if($c->company)<div style="font-size:12px;color:#718096">{{ $c->company }}</div>@endif
                    </td>
                    <td style="font-size:13px">
                        @if($c->email)<div>{{ $c->email }}</div>@endif
                        @if($c->phone)<div style="color:#718096">{{ $c->phone }}</div>@endif
                    </td>
                    <td style="font-size:13px;color:#718096">{{ $c->city ?? '-' }}</td>
                    <td>
                        <span class="badge" style="background:#ebf8ff;color:#2b6cb0">{{ $c->invoices_count }} invoice</span>
                    </td>
                    <td style="text-align:right">
                        <div style="display:flex;gap:6px;justify-content:flex-end">
                            <a href="{{ route('customers.show', $c) }}" class="btn btn-ghost btn-sm">Lihat</a>
                            <a href="{{ route('customers.edit', $c) }}" class="btn btn-outline btn-sm">Edit</a>
                            <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Hapus customer ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:#a0aec0">Belum ada customer. <a href="{{ route('customers.create') }}" style="color:#0099d8">Tambah sekarang</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div style="padding:16px 24px;border-top:1px solid #f0f4f8">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
