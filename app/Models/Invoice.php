<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Receipt;
class Invoice extends Model
{
    public $fillable = [
        'pelanggan_id',
        'invoice_number',
        'name',
        'amount',
        'due_date',
        'status'
    ];

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
}
