<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PaketResource\Pages;

use App\Filament\Clusters\PelangganManager\Resources\PaketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPakets extends ListRecords
{
    protected static string $resource = PaketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}