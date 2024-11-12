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
        return $this->hasOne(Pelanggan::class, "id", "pelanggan_id");
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class, "id", "tagihan_id");
    }
    public function operator(): HasOne
    {
        return $this->hasOne(User::class, "id", "user_id");
    }
    protected static function booted(): void
    {
        static::created(function (PembayaranPelanggan $pembayaran){
            $secret = $pembayaran->pelanggan->profil->secret;
            $secret->enable();
        });
    }
    protected function casts(): array
    {
        return [
            "nominal_tagihan" => "decimal:2"
        ];
    }
}
