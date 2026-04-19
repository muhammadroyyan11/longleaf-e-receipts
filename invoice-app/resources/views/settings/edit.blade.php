@extends('layouts.app')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Aplikasi')

@section('content')
<div style="max-width:640px">
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" id="settings-form">
        @csrf

        {{-- Identitas --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span style="font-weight:600">Identitas Perusahaan</span></div>
            <div class="card-body">
                @php $logo = \App\Models\Setting::get('company_logo') @endphp
                <div class="form-group">
                    <label>Logo Perusahaan</label>
                    @if($logo)
                    <div style="margin-bottom:10px;display:flex;align-items:center;gap:16px">
                        <img src="{{ Storage::url($logo) }}" alt="Logo" style="max-height:56px;max-width:180px;border:1px solid #e2e8f0;border-radius:6px;padding:6px">
                        <label style="display:flex;align-items:center;gap:6px;font-weight:400;color:#e53e3e;cursor:pointer;font-size:13px">
                            <input type="checkbox" name="remove_logo" value="1" style="width:auto"> Hapus logo
                        </label>
                    </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" style="padding:6px">
                    <p style="font-size:12px;color:#718096;margin-top:4px">Format: JPG, PNG, SVG. Maks 2MB.</p>
                </div>
                <div class="form-group">
                    <label>Nama Perusahaan <span style="color:#e53e3e">*</span></label>
                    <input type="text" name="company_name"
                        value="{{ old('company_name', \App\Models\Setting::get('company_name', 'InvoiceGen')) }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Tagline <span style="color:#a0aec0;font-weight:400">(opsional)</span></label>
                    <input type="text" name="company_tagline"
                        value="{{ old('company_tagline', \App\Models\Setting::get('company_tagline')) }}"
                        placeholder="Solusi terbaik untuk bisnis Anda">
                </div>
            </div>
        </div>

        {{-- Tanda Tangan Digital --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span style="font-weight:600">Tanda Tangan Digital</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Penandatangan</label>
                    <input type="text" name="signer_name"
                        value="{{ old('signer_name', \App\Models\Setting::get('signer_name')) }}"
                        placeholder="Nama lengkap penandatangan">
                </div>

                <label>Tanda Tangan</label>
                @php $existingSig = \App\Models\Setting::get('signature') @endphp
                @if($existingSig)
                <div style="margin-bottom:10px">
                    <p style="font-size:12px;color:#718096;margin-bottom:6px">Tanda tangan tersimpan:</p>
                    <img src="{{ $existingSig }}" alt="TTD" style="max-height:80px;border:1px solid #e2e8f0;border-radius:6px;padding:6px;background:#fff">
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;color:#e53e3e;cursor:pointer;font-size:13px;margin-top:8px">
                        <input type="checkbox" name="remove_signature" value="1" style="width:auto"> Hapus tanda tangan
                    </label>
                </div>
                @endif

                <p style="font-size:12px;color:#718096;margin-bottom:8px">Gambar tanda tangan baru di bawah (kosongkan jika tidak ingin mengubah):</p>
                <div style="position:relative;border:2px dashed #e2e8f0;border-radius:8px;background:#fafafa;display:inline-block">
                    <canvas id="sig-canvas" width="500" height="160" style="display:block;cursor:crosshair;border-radius:6px"></canvas>
                </div>
                <div style="margin-top:8px;display:flex;gap:8px">
                    <button type="button" onclick="clearSig()" class="btn btn-ghost btn-sm">Bersihkan</button>
                    <span style="font-size:12px;color:#a0aec0;align-self:center">Gunakan mouse atau sentuh layar untuk menandatangani</span>
                </div>
                <input type="hidden" name="signature_data" id="sig-data">
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end">
            <button type="submit" class="btn btn-primary" onclick="prepareSig()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const canvas = document.getElementById('sig-canvas');
const ctx = canvas.getContext('2d');
let drawing = false;

ctx.strokeStyle = '#1a202c';
ctx.lineWidth = 2;
ctx.lineCap = 'round';
ctx.lineJoin = 'round';

function getPos(e) {
    const r = canvas.getBoundingClientRect();
    const src = e.touches ? e.touches[0] : e;
    return { x: src.clientX - r.left, y: src.clientY - r.top };
}

canvas.addEventListener('mousedown',  e => { drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); });
canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
canvas.addEventListener('mouseup',    () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);
canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }, { passive: false });
canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
canvas.addEventListener('touchend',   () => drawing = false);

function clearSig() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('sig-data').value = '';
}

function isCanvasBlank() {
    return !canvas.toDataURL().includes('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA') === false
        && ctx.getImageData(0, 0, canvas.width, canvas.height).data.every(v => v === 0);
}

function prepareSig() {
    const pixels = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
    const hasContent = pixels.some((v, i) => i % 4 === 3 && v > 0);
    if (hasContent) {
        document.getElementById('sig-data').value = canvas.toDataURL('image/png');
    }
}
</script>
@endpush
