<?php

namespace App\Models\PPPoE;

use Sushi\Sushi;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use Sushi;
    protected $primaryKey = "id";
    protected $guarded = [];
    protected function sushiShouldCache()
    {
        return false;
    }
    public function getRows(): array
    {
        try {
            $response = Http::routeros()->get(
                "/ppp/profile"
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
        }catch(\Illuminate\Http\Client\ConnectionException $e){
            Notification::make("connection-failure")
                ->title("Connection Failure")
                ->body($e->getMessage())
                ->persistent()
                ->danger()
                ->send();
            return [];
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
            $response = Http::routeros()->put(
                "/ppp/profile",
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
