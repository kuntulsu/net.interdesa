<?php

namespace App\Models\Interface;

use Sushi\Sushi;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

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
        try{
            $response = Http::routeros()
                ->post("/interface/monitor-traffic", [
                    "interface" => $interface,
                    "once" => true
                ]);
            
            return $response->json();

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
}
