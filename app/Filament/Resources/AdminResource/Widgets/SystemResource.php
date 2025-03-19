<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use App\Models\Interface\Monitoring;
use App\Models\System\Resource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Number;

class SystemResource extends BaseWidget
{
    public $cpu_usage_history = [];
    public $mem_usage_history = [];
    public $traffic_usage_history = [];
    // protected int | string | array $columnSpan = 2;
    protected static ?string $pollingInterval = "5s";
    protected static bool $isLazy = false;
    protected static string $title = "System Resources";
    protected function getStats(): array
    {
        // dd(\App\Helpers\Helper::server_checkup());
        if (! \App\Helpers\Helper::server_checkup()){
            return [view('livewire.server-info')];
        }
        $resource = Resource::first();
        $monitoring = Monitoring::monitor("ether1");
        $cpu_usage = $resource['cpu-load'];
        $memory_usg = $resource['total-memory'] - $resource['free-memory'];
        $traffic_usg = $monitoring[0]['rx-bits-per-second'] / 1024 / 1024;
        $traffic_usg = Number::format($traffic_usg, 2);
        array_push($this->cpu_usage_history, $cpu_usage);
        array_push($this->mem_usage_history, $memory_usg);
        array_push($this->traffic_usage_history, $traffic_usg);
        $memory_usg_percent = Number::format($memory_usg/$resource['total-memory']*100, 2);
        return [
            Stat::make("CPU Usage", "$cpu_usage%")
            ->description("{$resource['cpu']} (x{$resource['cpu-count']}) {$resource['cpu-frequency']} MHz")
            ->chart($this->cpu_usage_history)
            // ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
            Stat::make("Memory Usage", "$memory_usg_percent%")
                ->chart($this->mem_usage_history)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
            Stat::make("Traffic Monitor", "$traffic_usg Mbps")
                ->chart($this->traffic_usage_history)
                ->color("warning")
                ->description("Download Traffic of {$monitoring[0]["name"]}")
                ->descriptionIcon("heroicon-m-arrow-trending-up")
        ];
    }
}
