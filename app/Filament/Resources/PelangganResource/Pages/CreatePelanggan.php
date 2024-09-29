<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use App\Filament\Resources\PelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePelanggan extends CreateRecord
{
    protected static string $resource = PelangganResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        return $data;
    }
    protected function handleRecordCreation(array $data): Model
    {
        $dataPelanggan = [
            "nik" => $data["nik"],
            "nama" => $data["nama"],
            "alamat" => $data["alamat"],
            "telp" => $data["telp"],
            "jatuh_tempo" => $data["jatuh_tempo"],
        ];
        // $dataSecret = [
        //     "name" => $data["profil"]["secret"]["name"],
        //     "password" => $data["profil"]["secret"]["password"],
        //     "profile" => $data["profil"]["secret"]["profile"],
        //     "local-address" => $data["profil"]["secret"]["local_address"],
        //     "remote-address" => $data["profil"]["secret"]["remote_address"],
        // ];

        $pelanggan = static::getModel()::create($dataPelanggan);

        if ($pelanggan) {
            // $secret = \App\Models\PPPoE\Secret::create($dataSecret);

            // $profile = \App\Models\ProfilPelanggan::create([
            //     "pelanggan_id" => $pelanggan->id,
            //     "secret_id" => $secret->id,
            // ]);
            $pelanggan->profil()->create([
                "secret_id" => $data["secret_id"],
            ]);
        }
        return $pelanggan;
    }
}
