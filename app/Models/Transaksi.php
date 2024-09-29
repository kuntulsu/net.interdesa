<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = "transaksi";
    protected $guarded = [];
    public function bukti(): HasMany
    {
        return $this->hasMany(BuktiTransaksi::class);
    }

    protected function casts(): array
    {
        return [
            "tipe" => \App\TipeTransaksi::class,
        ];
    }
}
