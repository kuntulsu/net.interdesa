<?php

namespace App\Models\System;

use Sushi\Sushi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class Resource extends Model
{
    use Sushi;

    public function getRows()
    {
        try{
            $response = Http::routeros()
                ->get("/system/resource");

            if($response->ok()){
                return [$response->json()];
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

        return [];

        
    }
}
