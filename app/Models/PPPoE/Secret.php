<?php

namespace App\Models\PPPoE;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Secret extends Model
{
    use \Sushi\Sushi;
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $schema = [
        "id" => "string",
        "name" => "string",
        "password" => "string",
        "profile" => "string",
        "disabled" => "string",
        "service" => "string",
        "local-address" => "string",
        "remote-address" => "string",
    ];
    protected function toSchema(
        array|Collection $data,
        bool $one = false
    ): array {
        $secrets = [];
        foreach ($data as $secret) {
            $filtered = [];
            foreach ($this->schema as $key => $value) {
                if ($key == "id") {
                    $filtered[$key] = $secret[".id"];
                    continue;
                }
                $filtered[$key] = data_get($secret, $key);
            }
            array_push($secrets, $filtered);
        }

        return $secrets;
    }

    protected function sushiShouldCache()
    {
        return false;
    }
    public function getRows()
    {
        try {
            $response = Http::withBasicAuth("admin", "admin")->get(
                "http://192.168.56.101/rest/ppp/secret"
            );
            $data = $response->collect();

            return $this->toSchema($data);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function profil(): HasOne
    {
        return $this->hasOne(\App\Models\ProfilPelanggan::class);
    }
    public function paket(): HasOne
    {
        return $this->hasOne(
            \App\Models\PPPoE\Profile::class,
            "name",
            "profile"
        );
    }
    public function active(): HasOne
    {
        return $this->hasOne(\App\Models\PPPoE\Active::class, "name", "name");
    }
    protected static function booted(): void
    {
        static::created(function (Secret $secret) {
            $data = [
                "name" => $secret->name,
                "password" => $secret->password,
            ];

            if (isset($data["profile"])) {
                $data["profile"] = $secret->profile;
            }

            if (
                isset($secret["local-address"]) &&
                isset($secret["remote-address"])
            ) {
                $data["local-address"] = $secret["local-address"];
                $data["remote-address"] = $secret["remote-address"];
            }

            $response = Http::withBasicAuth("admin", "admin")
                ->put("http://192.168.56.101/rest/ppp/secret", $data)
                ->json();
            $secret->id = $response[".id"];
            // \App\Models\ProfilPelanggan::create([
            //     "pelanggan_id" => $secret["pelanggan_id"],
            //     "secret_id" => $response[".id"],
            // ]);
        });
        static::updated(function (Secret $secret) {
            $data = [
                "name" => $secret->name,
                "password" => $secret->password,
                "profile" => $secret->profile,
                "local-address" => $secret["local-address"],
                "remote-address" => $secret["remote-address"],
            ];
            $active = $secret->active;
            $response = Http::withBasicAuth("admin", "admin")
                ->patch(
                    "http://192.168.56.101/rest/ppp/secret/{$secret->id}",
                    $data
                )
                ->json();
            $active?->dropConnection();
        });
    }
    protected function casts(): array
    {
        return [
            "id" => "string",
            "disabled" => \App\Casts\CustomBoolean::class,
        ];
    }
}
