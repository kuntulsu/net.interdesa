<?php

namespace App\Models\PPPoE;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Profile extends Model
{
    use Sushi;
    protected $primaryKey = "id";
    protected $guarded = [];
    public function getRows(): array
    {
        try {
            $response = Http::withBasicAuth("admin", "admin")->get(
                "http://192.168.56.101/rest/ppp/profile"
            );
            if ($response->ok()) {
                $profiles = [];
                foreach ($response->collect() as $profile) {
                    $arr = [];
                    $arr["id"] = $profile[".id"];
                    $arr["name"] = $profile["name"];
                    $arr["default"] = $profile["default"];
                    $arr["rate_limit"] = $profile["rate-limit"] ?? null;

                    array_push($profiles, $arr);
                }
                return $profiles;
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function harga()
    {
        return $this->hasOne(\App\Models\HargaPaket::class, "profile_id", "id");
    }
    protected static function booted(): void
    {
        static::created(function (Profile $profile) {
            $data = [
                "name" => $profile->name,
                "rate-limit" => $profile->rate_limit,
            ];
            $response = Http::withBasicAuth("admin", "admin")->put(
                "http://192.168.56.101/rest/ppp/profile",
                $data
            );
        });
    }
    protected function casts(): array
    {
        return [
            "id" => "string",
            "default" => \App\Casts\CustomBoolean::class,
        ];
    }
}
