<?php

namespace App\Filament\Resources\Healths;

use App\Filament\Resources\Healths\Pages\HealthIndex;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HealthResource extends Resource
{
    protected static ?string $model = \App\Models\Pelanggan::class;
    protected static ?string $navigationLabel = "Health";
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = "name";

    // public static function form(Schema $schema): Schema
    // {
    //     return HealthForm::configure($schema);
    // }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return HealthInfolist::configure($schema);
    // }

    // public static function table(Table $table): Table
    // {
    //     return HealthsTable::configure($table);
    // }

    // public static function getRelations(): array
    // {
    //     return [
    //             //
    //         ];
    // }
    public static function getWidgets(): array
    {
        return [\App\Filament\Widgets\ActiveClientWidget::class];
    }
    public static function getPages(): array
    {
        return [
            "index" => HealthIndex::route("/"),
        ];
    }
}
