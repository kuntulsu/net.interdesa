<?php

namespace App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pelanggan;
class ActiveClientWidget extends StatsOverviewWidget
{
    public $pelanggan;
    public $activeClientCount;
    public $activeIcon;
    public $activeIconColor;
    public $activeClientDescriptionText;
    public $offlineClientCount;
    protected function getStats(): array
    {
        $plg = Pelanggan::with([
            "profil" => fn($query) => $query->with([
                "secret" => fn($q) => $q->with("active"),
            ]),
        ])->get();
        $this->pelanggan = $plg->where("disabled", false);
        $this->activeClientCount = $this->pelanggan
            ->whereNotNull("profil.secret.active")
            ->count();
        $this->offlineClientCount =
            $this->pelanggan->count() - $this->activeClientCount;

        $this->activeClient();

        return [
            Stat::make("Active Client", $this->activeClientCount)->description(
                $this->activeClientDescriptionText,
            ),
        ];
    }
    public function activeClient(): string
    {
        if ($this->offlineClientCount->count() == 0) {
            $this->activeIcon = Heroicon::CheckCircle;
            $this->activeIconColor = "success";
            $this->activeClientDescriptionText = "100% Client Uptime!!";
        }
    }
}
