<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;
    protected $table = "tagihan";
    protected $guarded = [];

    public function terbayar()
    {
        return $this->belongsTo(PembayaranPelanggan::class, "id", "tagihan_id");
    }
}
