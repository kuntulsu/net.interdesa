<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use App\Filament\Resources\PelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPelanggan extends EditRecord
{
    protected static string $resource = PelangganResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $pelanggan = \App\Models\Pelanggan::find($data["id"]);
        $secret = $pelanggan->profil?->secret;
        if ($secret) {
            $data["secret"] = $secret?->toArray();
            $data["secret"]["id"] = $secret->id;
        }
        return $data;
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            "nama" => $data["nama"],
            "nik" => $data["nik"],
            "alamat" => $data["alamat"],
            "telp" => $data["telp"],
            "jatuh_tempo" => $data["jatuh_tempo"],
        ]);

        if (isset($data["secret"])) {
            $secret = \App\Models\PPPoE\Secret::find($data["secret"]["id"]);
            unset($data["secret"]["id"]);
            $secret->update($data["secret"]);
        }

        if (isset($data["secret_id"])) {
            \App\Models\ProfilPelanggan::create([
                "pelanggan_id" => $record->id,
                "secret_id" => $data["secret_id"],
            ]);
        }

        return $record;
    }
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
