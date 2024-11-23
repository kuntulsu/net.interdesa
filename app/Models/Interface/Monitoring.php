<?php

namespace App\Models\Interface;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Monitoring extends Model
{
    
    Use Sushi;

    public function getRows()
    {
        $interface = Http::routeros()
            ->get("/interface");
        dd($interface->json());
        // return $interface->json();

    }

    public static function monitor($interface)
    {
        $response = Http::routeros()
            ->post("/interface/monitor-traffic", [
                "interface" => $interface,
                "once" => true
            ]);
        
        return $response->json();
    }
}
