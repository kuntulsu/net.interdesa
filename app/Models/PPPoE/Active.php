<?php

namespace App\Models\PPPoE;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Active extends Model
{
    use Sushi;
    protected $schema = [
        "id" => "string",
        "name" => "string",
    ];
    protected function sushiShouldCache()
    {
        return false;
    }
    public function dropConnection()
    {
        try {
            $response = Http::withBasicAuth("admin", "admin")->delete(
                "http://192.168.56.101/rest/ppp/active/{$this->id}"
            );
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function getRows(): array
    {
        try {
            $response = Http::withBasicAuth("admin", "admin")->get(
                "http://192.168.56.101/rest/ppp/active"
            );
            $data = $response->collect();
            $actives = [];
            foreach ($data as $active) {
                $filtered = [];

                $filtered["id"] = $active[".id"];
                $filtered["address"] = $active["address"] ?? null;
                $filtered["caller-id"] = $active["caller-id"] ?? null;
                $filtered["name"] = $active["name"] ?? null;
                $filtered["uptime"] = $active["uptime"] ?? null;

                array_push($actives, $filtered);
            }
            return $actives;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    protected function casts(): array
    {
        return [
            "id" => "string",
        ];
    }
}
