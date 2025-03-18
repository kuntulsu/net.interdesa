<?php

namespace App\Livewire;

use App\Models\Tagihan;
use Livewire\Component;
use Filament\Notifications\Notification;

class PaymentOverview extends Component
{
    public \App\Models\Pelanggan $ownerRecord;
    public \App\Models\Tagihan $record;
    public \App\PaymentMethodEnum | string $payment_method;
    public $diskon;

    public function pay()
    {
        $tagihan = $this->record;
        $pelanggan = $this->ownerRecord;
        $harga_paket = $this->ownerRecord->profil->secret->paket->harga?->harga;
        
        $pembayaran = \App\Models\PembayaranPelanggan::create([
            "pelanggan_id" => $pelanggan->id,
            "tagihan_id" => $tagihan->id,
            "nominal_tagihan" => ($this->diskon == "pemasangan") ? $harga_paket/2 : $harga_paket,
            "payment_method" => $this->payment_method,
            "user_id" => auth()->user()->id
        ]);

        if($pembayaran) {
            Notification::make("payment_success")
            ->success()
            ->title("Pembayaran Berhasil")
            ->send();
        }else {
            Notification::make("payment_failed")
            ->danger()
            ->title("Pembayaran Gagal")
            ->send();
        }
        
    }
    public function mount ($ownerRecord = null, $record)
    {
        $this->ownerRecord = $ownerRecord;
        $this->record = $record;
        $this->payment_method = \App\PaymentMethodEnum::Tunai;
    }
    public function render()
    {
        return view('livewire.payment-overview');
    }
}
