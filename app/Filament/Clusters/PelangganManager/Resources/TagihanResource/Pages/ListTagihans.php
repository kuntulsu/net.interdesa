<?php

namespace App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTagihans extends ListRecords
{
    protected static string $resource = TagihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
