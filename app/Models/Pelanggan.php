<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Pelanggan extends Model
{
    use HasFactory;
    protected $table = "pelanggan";
    protected $guarded = [];
    // protected $with = ["profil"];
    public function profil(): HasOne
    {
        return $this->hasOne(ProfilPelanggan::class, "pelanggan_id");
    }
    public function secret()
    {
        return $this->hasOneThrough(
            PPPoE\Secret::class,
            ProfilPelanggan::class,
            "pelanggan_id",
            "id",
            "id",
            "secret_id"
        );
    }
    public function scopeNotWhitelist(Builder $query): void
    {
        $query->where("whitelist", false);
    }
    public function odp_pelanggan(): HasManyThrough
    {
        return $this->hasManyThrough(Pelanggan::class, ODP::class);
    }
    public function odp(): HasOne
    {
        return $this->hasOne(ODP::class, "id", "odp_id");
    }
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }
    // public function tagihan()
    // {
    //     // return $this->hasMany(Tagihan::class);
    //     return $this->hasManyThrough(
    //         Tagihan::class, // The target model
    //         PembayaranPelanggan::class, // The intermediary model
    //         "pelanggan_id", // Foreign key on the payments table
    //         "id", // Foreign key on the bills table
    //         "id", // Local key on the customers table
    //         "tagihan_id" // Local key on the payments table
    //     );
    // }
    public function isolirPelanggan()
    {
        $secret = $this->profil->secret;
        
        dd($secret);
    }
    public function pembayaran()
    {
        return $this->hasMany(PembayaranPelanggan::class);
    }
    protected function casts(): array
    {
        return [
            "telp" => \App\Casts\Telp::class,
            "whitelist" => "boolean"
        ];
    }
}
