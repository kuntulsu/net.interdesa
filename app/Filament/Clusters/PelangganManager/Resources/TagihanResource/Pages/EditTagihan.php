<?php

namespace App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTagihan extends EditRecord
{
    protected static string $resource = TagihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
