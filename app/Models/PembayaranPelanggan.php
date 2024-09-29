<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PembayaranPelanggan extends Model
{
    use HasFactory;
    protected $table = "pembayaran_pelanggan";
    protected $guarded = [];

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class);
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class);
    }
    public function operator(): HasOne
    {
        return $this->hasOne(User::class, "id", "user_id");
    }
}
