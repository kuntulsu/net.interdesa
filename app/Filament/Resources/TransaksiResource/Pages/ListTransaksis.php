<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksis extends ListRecords
{
    // use \BezhanSalleh\FilamentShield\Traits\HasPageShield;

    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
