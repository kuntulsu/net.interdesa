<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use App\Filament\Clusters\PelangganManager;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\RelationManagers;
use App\Models\Paket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextInputColumn;

class PaketResource extends Resource
{
    protected static ?string $model = \App\Models\PPPoE\Profile::class;

    protected static ?string $navigationIcon = "heroicon-o-cube";

    // protected static ?string $cluster = PelangganManager::class;
    protected static ?string $navigationGroup = 'Pelanggan Manager';


    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make("name"),
            TextInput::make("harga.harga"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name"),
                TextInputColumn::make("harga.harga")
                    ->type("number")
                    ->inputMode("decimal")
                    ->beforeStateUpdated(function ($record, $state) {
                        $harga = \App\Models\HargaPaket::where(
                            "profile_id",
                            $record->id
                        )->first();
                        if (!$harga) {
                            \App\Models\HargaPaket::create([
                                "profile_id" => $record->id,
                                "harga" => 0,
                            ]);
                        }
                    })
                    ->afterStateUpdated(fn() => Notification::make("save_success")
                        ->success()
                        ->title("Saved Successfully")
                        ->send()
                    ),

                // TextColumn::make("harga.harga")->money("IDR"),
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
            "index" => Pages\ListPakets::route("/"),
            "create" => Pages\CreatePaket::route("/create"),
            "edit" => Pages\EditPaket::route("/{record}/edit"),
        ];
    }
}
