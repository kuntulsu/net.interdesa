<?php

namespace App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\TipeTagihanEnum;
use Filament\Notifications\Notification;

class CreateTagihan extends CreateRecord
{
    protected static string $resource = TagihanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if(isset($data['pelanggan_id'])){
            $pelanggan = Pelanggan::find($data['pelanggan_id']);

            if(!$pelanggan) {
                Notification::make("pelanggan_not_found")
                    ->danger()
                    ->title("Pelanggan Tidak Ditemukan");
                abort(404);
            }

            $tagihan = $pelanggan->tagihan()->create([
                "name" => $data['name'],
                "tipe_tagihan" => TipeTagihanEnum::to($data['tipe_tagihan'])?->value,
                "nominal_tagihan" => $data["nominal_tagihan"] ?? 0,
                "end_date" => $data["end_date"] ?? null
            ]);

            return $tagihan;
        }
        if(isset($data['tipe_tagihan']) && $data['tipe_tagihan'] == TipeTagihanEnum::BULANAN->name){
            $tagihan = Tagihan::create([
                "pelanggan_id" => null,
                "name" => $data['name'],
                "tipe_tagihan" => TipeTagihanEnum::to($data['tipe_tagihan']),
                "nominal_tagihan" => 0,
                "end_date" => $data['end_date'] ?? null
            ]);
            return $tagihan;
        }
        
    }
}
