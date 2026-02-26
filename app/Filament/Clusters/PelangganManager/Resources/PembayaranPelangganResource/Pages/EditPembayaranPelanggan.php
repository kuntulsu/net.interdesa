<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranPelanggan extends EditRecord
{
    protected static string $resource = PembayaranPelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
