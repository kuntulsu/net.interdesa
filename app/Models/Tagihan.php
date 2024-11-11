<?php

namespace App\Models;

use App\TipeTagihanEnum;
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
        return $this->hasMany(PembayaranPelanggan::class);
    }
    public function lunas(int|bool $pelanggan_id = null)
    {
        $tagihan = 0;
        $terbayar = 0;
        if(is_null($pelanggan_id)){
            $tagihan = $this->nominal_tagihan ?? 0;
            $terbayar = $this->pembayaran()->sum("nominal_tagihan") ?? 0;
        }
        if($pelanggan_id && $this->tipe_tagihan == TipeTagihanEnum::BULANAN) {
            $pelanggan = Pelanggan::find($pelanggan_id);
            $pembayaran = $this->pembayaran()->where("pelanggan_id", $pelanggan_id)->first();
            $tagihan = $pelanggan->profil->secret->paket->harga?->harga;
            $terbayar = $pembayaran->nominal_tagihan ?? 0;
        }
        return (float)$tagihan <= (float)$terbayar;
        // return $this->hasOne(PembayaranPelanggan::class);
    }

    protected function casts(): array
    {
        return [
            "tipe_tagihan" => \App\TipeTagihanEnum::class,
            "nominal_tagihan"=> "decimal:2"
        ];
    }
    protected static function boot()
    {
        parent::boot();

        // Set default sort order by 'created_at' column in descending order
        static::addGlobalScope('defaultSort', function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }
}
