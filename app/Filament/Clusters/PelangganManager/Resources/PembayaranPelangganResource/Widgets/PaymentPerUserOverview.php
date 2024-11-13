<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Widgets;

use App\Models\User;
use Illuminate\Support\Number;
use App\Models\PembayaranPelanggan;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PaymentPerUserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $users = User::withSum("payment_handled", "nominal_tagihan")->get();
        $widgets = [];
        foreach($users as $user){
            $total_tagihan_handled = $user->payment_handled_sum_nominal_tagihan ?? 0;
            $stat = Stat::make(
                str($user->name)->upper(),
                "IDR ".Number::format($total_tagihan_handled, 2))
                    ->color("success")
                    ->description("Payment Handled");
            array_push($widgets, $stat);
        }
        return $widgets;
    }
}
