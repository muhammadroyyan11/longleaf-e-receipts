<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name'    => 'required|string|max:255',
            'company_tagline' => 'nullable|string|max:255',
            'signer_name'     => 'nullable|string|max:255',
            'logo'            => 'nullable|image|max:2048',
        ]);

        Setting::set('company_name', $request->company_name);
        Setting::set('company_tagline', $request->company_tagline);
        Setting::set('signer_name', $request->signer_name);

        if ($request->hasFile('logo')) {
            $old = Setting::get('company_logo');
            if ($old && \Storage::disk('public')->exists($old)) {
                \Storage::disk('public')->delete($old);
            }
            Setting::set('company_logo', $request->file('logo')->store('logo', 'public'));
        }

        if ($request->boolean('remove_logo')) {
            $old = Setting::get('company_logo');
            if ($old && \Storage::disk('public')->exists($old)) {
                \Storage::disk('public')->delete($old);
            }
            Setting::set('company_logo', null);
        }

        // Signature: base64 data URL dari canvas
        if ($request->filled('signature_data')) {
            Setting::set('signature', $request->signature_data);
        }

        if ($request->boolean('remove_signature')) {
            Setting::set('signature', null);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
