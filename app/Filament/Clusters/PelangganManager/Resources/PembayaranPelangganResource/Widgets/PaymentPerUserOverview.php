<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Widgets;

use App\Models\User;
use App\Models\Tagihan;
use Illuminate\Support\Number;
use App\Models\PembayaranPelanggan;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PaymentPerUserOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    use InteractsWithTable;

    protected function getStats(): array
    {
        $tagihan_aktif = Tagihan::first();
        $users = User::withSum(["payment_handled" => function ($query) use($tagihan_aktif) {
            $query->where("tagihan_id", $tagihan_aktif->id);
        }], "nominal_tagihan")->get();
        $widgets = [];
        foreach($users as $user){
            $total_tagihan_handled = $user->payment_handled_sum_nominal_tagihan ?? 0;
            $stat = Stat::make(
                str($user->name)->upper(),
                "IDR ".Number::format($total_tagihan_handled, 2))
                    ->color("success")
                    ->description($tagihan_aktif->name);
            array_push($widgets, $stat);
        }
        return $widgets;
    }
}
