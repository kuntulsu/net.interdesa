<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfilPelanggan extends Model
{
    use HasFactory;
    protected $table = "profil_pelanggan";
    // protected $with = ["secret"];
    protected $guarded = [];

    public function secret(): HasOne
    {
        return $this->hasOne(
            \App\Models\PPPoE\Secret::class,
            "id",
            "secret_id"
        );
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, "pelanggan_id");
    }
    // public function secret()
    // {
    //     return $this->belongsTo(PPPoE\Secret::class);
    // }
}
