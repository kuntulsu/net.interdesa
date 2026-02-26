<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaPaket extends Model
{
    use HasFactory;
    protected $table = "harga_paket";
    protected $guarded = [];
}
