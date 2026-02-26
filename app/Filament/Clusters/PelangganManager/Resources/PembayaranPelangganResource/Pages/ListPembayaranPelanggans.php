<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Widgets\PaymentPerUserOverview;

class ListPembayaranPelanggans extends ListRecords
{

    protected static string $resource = PembayaranPelangganResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            PaymentPerUserOverview::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
