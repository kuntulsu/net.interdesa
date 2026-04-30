<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class ACS extends Model
{
    protected $table = "acs";
    use Sushi;

    public function getRows(): array
    {
        $req = Http::get(
            "http://192.168.123.2:7557/devices?projection=VirtualParameters",
        )->collect();

        $rows = $req->map(function ($item) {
            return [
                "_id" => data_get($item, "_id"),
                "pppoeUsername" => data_get(
                    $item,
                    "VirtualParameters.pppoeUsername._value",
                ),
                "pppoePassword" => data_get(
                    $item,
                    "VirtualParameters.pppoePassword._value",
                ),
                "pppoeMac" => data_get(
                    $item,
                    "VirtualParameters.pppoeMac._value",
                ),
                "pppoeIP" => data_get(
                    $item,
                    "VirtualParameters.pppoeIP._value",
                ),
                "deviceTemp" => data_get(
                    $item,
                    "VirtualParameters.gettemp._value",
                ),
                "RXPower" => data_get(
                    $item,
                    "VirtualParameters.RXPower._value",
                ),
            ];
        });

        return $rows->toArray();
    }
    public function secret()
    {
        return $this->hasOne(
            \App\Models\PPPoE\Secret::class,
            "name",
            "pppoeUsername",
        );
    }
}
