<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'description', 'amount', 'is_taxed'];

    protected $casts = ['is_taxed' => 'boolean'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
