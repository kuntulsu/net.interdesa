<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $name = "LAPORAN INTERNET DESA PECANGAAN BULAN %s %d";
        $data["name"] = sprintf($name, $data["month"]->name, $data["year"]);

        return $data;
    }
}
