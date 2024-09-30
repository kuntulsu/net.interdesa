<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;
    protected $table = "tagihan";
    protected $guarded = [];

    // public function terbayar()
    // {
    //     return $this->belongsTo(PembayaranPelanggan::class, "id", "tagihan_id");
    // }
    public function pembayaran()
    {
        return $this->hasOne(PembayaranPelanggan::class);
    }
    public function lunas()
    {
        $tagihan = $this->nominal_tagihan ?? 0;
        $terbayar = $this->pembayaran?->nominal_tagihan ?? 0;
        return (float) $tagihan <= (float) $terbayar;
        // return $this->hasOne(PembayaranPelanggan::class);
    }

    protected function casts(): array
    {
        return [
            "tipe_tagihan" => \App\TipeTagihanEnum::class,
            "nominal_tagihan"=> "decimal:2"
        ];
    }
}
