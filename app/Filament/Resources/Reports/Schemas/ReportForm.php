<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\MonthEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("month")
                ->options(MonthEnum::class)
                ->native(false)
                ->default(now()->month),
            TextInput::make("year")->default(now()->year)->numeric(),
        ]);
    }
}
