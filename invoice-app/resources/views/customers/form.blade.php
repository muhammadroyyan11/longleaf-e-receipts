@extends('layouts.app')
@section('title', isset($customer->id) ? 'Edit Customer' : 'Tambah Customer')
@section('page-title', isset($customer->id) ? 'Edit Customer' : 'Tambah Customer')

@section('content')
<div style="max-width:700px">
    <form method="POST" action="{{ isset($customer->id) ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if(isset($customer->id)) @method('PUT') @endif

        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Kontak <span style="color:#e53e3e">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required placeholder="Muhammad Royyan">
                    </div>
                    <div class="form-group">
                        <label>Perusahaan <span style="color:#a0aec0;font-weight:400">(opsional)</span></label>
                        <input type="text" name="company" value="{{ old('company', $customer->company) }}" placeholder="PT. Contoh">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat <span style="color:#e53e3e">*</span></label>
                    <textarea name="address" required>{{ old('address', $customer->address) }}</textarea>
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Kota</label>
                        <input type="text" name="city" value="{{ old('city', $customer->city) }}" placeholder="Malang">
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $customer->province) }}" placeholder="Jawa Timur">
                    </div>
                    <div class="form-group">
                        <label>Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" placeholder="65162">
                    </div>
                </div>
                <div class="form-group">
                    <label>Negara</label>
                    <input type="text" name="country" value="{{ old('country', $customer->country ?? 'Indonesia') }}">
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Catatan <span style="color:#a0aec0;font-weight:400">(internal)</span></label>
                    <textarea name="notes" placeholder="Catatan internal tentang customer ini...">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div style="margin-top:20px;display:flex;gap:12px;justify-content:flex-end">
            <a href="{{ route('customers.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ isset($customer->id) ? 'Simpan Perubahan' : 'Tambah Customer' }}
            </button>
        </div>
    </form>
</div>
@endsection
