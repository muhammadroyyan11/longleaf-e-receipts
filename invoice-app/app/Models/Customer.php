<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'company', 'name', 'email', 'phone',
        'address', 'city', 'province', 'postal_code', 'country', 'notes',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company ? "{$this->company} — {$this->name}" : $this->name;
    }
}
