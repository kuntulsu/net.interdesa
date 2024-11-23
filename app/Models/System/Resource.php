<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Resource extends Model
{
    use Sushi;

    public function getRows()
    {
        $resource = Http::routeros()
            ->get("/system/resource");
        return [$resource->json()];
    }
}
