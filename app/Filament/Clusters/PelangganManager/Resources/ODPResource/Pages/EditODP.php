<?php

namespace App\Filament\Clusters\PelangganManager\Resources\ODPResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\PelangganManager\Resources\ODPResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditODP extends EditRecord
{
    protected static string $resource = ODPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
