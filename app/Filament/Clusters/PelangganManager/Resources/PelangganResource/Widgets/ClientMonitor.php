<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PelangganResource\Widgets;

use App\Models\Pelanggan;
use App\Models\PPPoE\Secret;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;
use App\Models\Interface\Monitoring;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ClientMonitor extends BaseWidget
{
    public Pelanggan $record;
    public Secret $secret;
    public array $downloadHistory = [];
    public array $uploadHistory = [];
    protected function getStats(): array
    {
        // tx = download
        // rx = upload
        $this->_boot();

        if (!$this->isClientActive()) {
            return [
                view("components.client-offline-indicator", [
                    "secretDisabled" => $this->secret->disabled,
                ]),
            ];
        }
        $monitoring = Monitoring::monitor($this->secret->findInterface());
        $ping = Monitoring::ping($this->secret->active?->address);
        // dd([$monitoring, $ping]);

        $downloadTraffic = $monitoring[0]["tx-bits-per-second"] / 1024 / 1024;
        $downloadTraffic = Number::format($downloadTraffic, 2);
        array_push($this->downloadHistory, $downloadTraffic);

        $uploadTraffic = $monitoring[0]["rx-bits-per-second"] / 1024 / 1024;
        $uploadTraffic = Number::format($uploadTraffic, 2);
        array_push($this->uploadHistory, $uploadTraffic);

        $latencies = $ping[0]["time"] ?? $ping[0]["status"];
        return [
            Stat::make("Download", "{$downloadTraffic} Mbps")
                ->description("{$this->secret->name}")
                ->color("success")
                ->icon("heroicon-o-arrow-down")
                ->chart($this->downloadHistory),
            Stat::make("Upload", "{$uploadTraffic} Mbps")
                ->description($this->secret->name)
                ->color("warning")
                ->icon("heroicon-o-arrow-up")
                ->chart($this->uploadHistory),
            Stat::make("Latencies", $latencies)
                ->description("Host: {$ping[0]["host"]}")
                ->color("info")
                ->icon("heroicon-o-arrows-up-down"),
        ];
    }
    protected function _boot()
    {
        $this->getSecret();
    }
    protected function isClientActive()
    {
        $this->getSecret();
        return $this->secret->active;
    }

    protected function getSecret()
    {
        $this->secret = $this->record->profil->secret;
    }
}
