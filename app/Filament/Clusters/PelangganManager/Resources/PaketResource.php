<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use App\Models\PPPoE\Profile;
use Filament\Schemas\Schema;
use App\Models\HargaPaket;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\Pages\ListPakets;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\Pages\CreatePaket;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\Pages\EditPaket;
use App\Filament\Clusters\PelangganManager;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\PaketResource\RelationManagers;
use App\Models\Paket;
use Filament\Forms;
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
    protected static ?string $model = Profile::class;

    protected static string | \BackedEnum | null $navigationIcon = "heroicon-o-cube";

    // protected static ?string $cluster = PelangganManager::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Pelanggan Manager';


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
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
                        $harga = HargaPaket::where(
                            "profile_id",
                            $record->id
                        )->first();
                        if (!$harga) {
                            HargaPaket::create([
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
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            "index" => ListPakets::route("/"),
            "create" => CreatePaket::route("/create"),
            "edit" => EditPaket::route("/{record}/edit"),
        ];
    }
}
