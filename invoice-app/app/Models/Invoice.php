<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'customer_id', 'invoice_number', 'status', 'invoice_date', 'due_date', 'currency',
        'client_company', 'client_name', 'client_address',
        'client_city', 'client_province', 'client_postal_code', 'client_country',
        'company_name', 'company_address', 'company_npwp',
        'subtotal', 'taxable_base', 'vat', 'total',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'due_date'     => 'date',
    ];

    public function isOverdue(): bool
    {
        return $this->status !== 'paid'
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function dueDaysLabel(): string
    {
        if (!$this->due_date || $this->status === 'paid') return '';
        $diff = now()->startOfDay()->diffInDays($this->due_date->copy()->startOfDay(), false);
        if ($diff < 0) return abs($diff) . ' hari terlambat';
        if ($diff === 0) return 'Jatuh tempo hari ini';
        return 'Jatuh tempo ' . $diff . ' hari lagi';
    }

    public function formatMoney(float $amount): string
    {
        $currency = $this->currency ?? 'IDR';
        $symbols  = ['IDR' => 'Rp', 'USD' => '$', 'SGD' => 'S$', 'EUR' => '€', 'MYR' => 'RM', 'GBP' => '£'];
        $symbol   = $symbols[$currency] ?? $currency . ' ';
        $decimals = $currency === 'IDR' ? 0 : 2;
        return $symbol . ' ' . number_format($amount, $decimals, '.', ',');
    }

    public static function currencies(): array
    {
        return ['IDR' => 'IDR — Rupiah', 'USD' => 'USD — US Dollar', 'SGD' => 'SGD — Singapore Dollar', 'EUR' => 'EUR — Euro', 'MYR' => 'MYR — Ringgit', 'GBP' => 'GBP — British Pound'];
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid'    => '<span class="badge-paid">PAID</span>',
            'unpaid'  => '<span class="badge-unpaid">UNPAID</span>',
            default   => '<span class="badge-pending">PENDING</span>',
        };
    }

    public function recalculate(): void
    {
        $items = $this->items;
        $subtotal     = $items->sum('amount');
        $taxableBase  = $items->where('is_taxed', true)->sum('amount');
        $vat          = round($taxableBase * 0.11, 2);
        $this->update([
            'subtotal'     => $subtotal,
            'taxable_base' => $taxableBase,
            'vat'          => $vat,
            'total'        => $subtotal + $vat,
        ]);
    }
}
