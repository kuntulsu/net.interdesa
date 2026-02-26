<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ODP extends Model
{
    protected $table = "odp";
    protected $guarded = [];


    public function pelanggan(): HasMany
    {
        return $this->hasMany(Pelanggan::class, "odp_id", "id");
    }
}
