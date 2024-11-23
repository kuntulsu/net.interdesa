<?php

namespace App\Filament\Clusters\PelangganManager\Resources\ODPResource\Pages;

use App\Filament\Clusters\PelangganManager\Resources\ODPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListODPS extends ListRecords
{
    protected static string $resource = ODPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
