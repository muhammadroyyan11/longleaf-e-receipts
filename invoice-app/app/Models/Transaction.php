<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['invoice_id', 'transaction_date', 'gateway', 'transaction_id', 'amount'];

    protected $casts = ['transaction_date' => 'date'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
