<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use App\Filament\Clusters\PelangganManager;
use App\Filament\Clusters\PelangganManager\Resources\ODPResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\ODPResource\RelationManagers;
use App\Filament\Clusters\PelangganManager\Resources\ODPResource\RelationManagers\PelangganRelationManager;
use App\Models\ODP;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ODPResource extends Resource
{
    protected static ?string $model = ODP::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = "odp";
    // protected static ?string $cluster = PelangganManager::class;
    protected static ?string $navigationGroup = 'Pelanggan Manager';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("nama")
                    ->label("Nama ODP"),
                TextInput::make("description")
                    ->nullable()
                    ->label("Deskripsi Lokasi ODP"),
                TextInput::make("slot")
                    ->label("Jumlah Slot ODP")
                    ->numeric(),
                TextInput::make("coordinate")
                    ->label("Coordinate")
                    ->helperText("Dapatkan data Latitude dan Longitude di Google Maps, Contoh: (-6.6874964090924465, 110.70726096920588)")
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query){
                return $query->withCount("pelanggan");
            })
            ->columns([
                TextColumn::make("Identificator")
                    ->getStateUsing(function ($record){
                        return "ODP-{$record->id}";
                    }),
                TextColumn::make("nama")
                    ->description(fn($record) => $record->description),
                TextColumn::make("slot")
                    ->label("Max Slot")->badge()->alignCenter(),
                TextColumn::make("pelanggan_count")
                    ->label("Pelanggan")
                    ->badge(),

                IconColumn::make("coordinate")
                    ->label("Lokasi")
                    ->alignCenter()
                    ->icon("heroicon-o-map")
                    ->url(fn($record) => "https://maps.google.com/?q={$record->coordinate}")
                    ->openUrlInNewTab()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PelangganRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListODPS::route('/'),
            'create' => Pages\CreateODP::route('/create'),
            'edit' => Pages\EditODP::route('/{record}/edit'),
        ];
    }
}
