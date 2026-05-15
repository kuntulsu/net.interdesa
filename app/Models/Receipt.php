<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'invoice_id',
        'amount_paid',
        'user_id',
        'payment_method'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function casts()
    {
        return [
            "payment_method" => \App\Casts\PaymentMethod::class,
        ];
    }
}
