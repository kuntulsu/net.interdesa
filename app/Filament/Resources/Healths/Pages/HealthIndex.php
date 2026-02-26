<?php

namespace App\Filament\Resources\Healths\Pages;

use App\Filament\Resources\Healths\HealthResource;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Widgets\ActiveClientWidget;
class HealthIndex extends Page
{
    protected static string $resource = HealthResource::class;

    protected string $view = "filament.resources.healths.pages.health-index";

    public function overviewInfolist(Schema $schema)
    {
        return $schema
            ->record(["name" => "kuntulsu"])
            ->columns(12)
            ->components([
                Section::make("Performa Bank Sampah Amanah")
                    ->description("Kompetensi Performa Penanganan Sampah")
                    ->columnSpanFull()
                    ->icon(Heroicon::ArrowTrendingUp)
                    ->schema([TextEntry::make("name")]),
            ]);
    }
    public function getHeaderWidgets(): array
    {
        return [ActiveClientWidget::class];
    }
}
