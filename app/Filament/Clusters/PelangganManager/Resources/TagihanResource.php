<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use App\Filament\Clusters\PelangganManager;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\RelationManagers;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;

    protected static ?string $navigationIcon = "heroicon-o-clipboard-document-list";

    protected static ?string $cluster = PelangganManager::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make("name")->label("Judul Tagihan"),
            DatePicker::make("end_date")->label("Jatuh Tempo Tagihan"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn($query) => $query->withSum("terbayar", "nominal_tagihan")
            )
            ->columns([
                TextColumn::make("name")->label("Judul Tagihan"),
                TextColumn::make("terbayar_count")
                    ->label("Pelanggan Membayar")
                    ->counts("terbayar"),
                TextColumn::make("terbayar_sum_nominal_tagihan")
                    ->label("Total Terbayar")
                    ->money("IDR"),
            ])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListTagihans::route("/"),
            "create" => Pages\CreateTagihan::route("/create"),
            "edit" => Pages\EditTagihan::route("/{record}/edit"),
        ];
    }
}
