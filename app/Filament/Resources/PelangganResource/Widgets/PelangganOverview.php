<?php

namespace App\Filament\Resources\PelangganResource\Widgets;

use \App\Models\Tagihan;
use \App\TipeTagihanEnum;
use \App\Models\Pelanggan;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\PelangganResource\Pages\ListPelanggans;
use App\Models\ProfilPelanggan;
use Illuminate\Support\Number;

class PelangganOverview extends BaseWidget
{

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $pelanggan_count = Pelanggan::count();

        $tagihan = Tagihan::where("tipe_tagihan", TipeTagihanEnum::BULANAN)->first();
        $pembayaran = $tagihan?->pembayaran;
        $progress_tagihan_percent = ($pembayaran?->count() <= 0 || $pelanggan_count <= 0) 
            ? 0
            : ($pembayaran?->count() / $pelanggan_count * 100); 
        $total_tagihan_terbayar = "IDR ".Number::format($tagihan?->pembayaran()?->sum("nominal_tagihan") ?? 0, 2);
        // $sum_total_tagihan = ProfilPelanggan::with("secret.paket.harga")->get()->sum("secret.paket.harga.harga");
        $ekspektasi_total_tagihan = "IDR ".Number::format(0, 2);
        return [    
            Stat::make('Total Pelanggan', $pelanggan_count)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Persentase Pelanggan Membayar', number_format($progress_tagihan_percent, 2)." %")
                ->description($tagihan?->name)
                // ->descriptionIcon('heroicon-m-arrow-trending-down')
                // ->color('danger')
                ,
            Stat::make('Tagihan Terbayar', $total_tagihan_terbayar)
                ->description("Dari: $ekspektasi_total_tagihan")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
