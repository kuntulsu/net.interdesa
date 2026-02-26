<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksi extends EditRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
