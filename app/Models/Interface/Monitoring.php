<?php

namespace App\Models\Interface;

use Illuminate\Support\Facades\Http;

class Monitoring
{
    
    public string $interface;
    public string $id;

    function __construct($interface = null)
    {
        $this->interface = $interface;

    }
    public static function interface($interface)
    {
        
        return new static($interface);
    }
    
    public function fetch()
    {
        $response = Http::withBasicAuth("admin","admin")->get("http://192.168.56.101/rest/interface/{$this->interface}");
        
        if($response->ok()){
            return $response->json();
        }

    }
}
