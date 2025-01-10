<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PelangganResource;

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

        $pelanggan = DB::transaction(function () use($data, $dataPelanggan) {
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
            Log::debug($pelanggan);
            return $pelanggan;
        });
        return $pelanggan;
    }
}
