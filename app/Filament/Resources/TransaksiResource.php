<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TransaksiResource\Pages\ListTransaksis;
use App\Filament\Resources\TransaksiResource\Pages\CreateTransaksi;
use App\Filament\Resources\TransaksiResource\Pages\EditTransaksi;
use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use Filament\Forms\Components\Select;
use App\TipeTransaksi;

class TransaksiResource extends Resource
{
    // use \BezhanSalleh\FilamentShield\Traits\HasPageShield;

    protected static ?string $model = Transaksi::class;

    protected static string | \BackedEnum | null $navigationIcon = "heroicon-o-arrows-up-down";
    protected static ?int $navigationSort = 3;
    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make("keterangan")
                ->required()
                ->label("Keterangan Transaksi"),
            TextInput::make("jumlah")
                ->numeric()
                ->inputMode("decimal")
                ->label("Besar Transaksi")
                ->required()
                ->prefix("IDR"),
            Select::make("tipe")
                ->options(TipeTransaksi::class)
                ->native(false)
                ->required()
                ->label("Tipe Transaksi"),
            DatePicker::make("created_at")
                ->native(false)
                ->required()
                ->label("Tanggal Transaksi"),

            Repeater::make("bukti")
                ->label("Bukti Transaksi")
                ->relationship()
                ->columnSpanFull()
                ->collapsible()
                ->required()
                ->schema([
                    FileUpload::make("bukti")
                        ->label("Gambar Untuk Bukti Transaksi")
                        ->required()
                        ->previewable(true)
                        ->image()
                        ->imageEditor(),
                    TextInput::make("keterangan")->label("Keterangan Bukti"),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("tipe")->badge()->color(
                    fn(TipeTransaksi $state): string => match (
                        $state->value
                    ) {
                        "KELUAR" => "warning",
                        "MASUK" => "success",
                    }
                ),
                TextColumn::make("keterangan"),
                TextColumn::make("jumlah")
                    ->money("IDR")
                    ->label("Besar Transaksi"),
                TextColumn::make("bukti_count")
                    ->label("Jumlah Bukti")
                    ->counts("bukti")
                    ->color("success")
                    ->badge(),
                TextColumn::make("created_at")
                    ->label("Tanggal Transaksi")
                    ->date("d F Y"),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
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
            "index" => ListTransaksis::route("/"),
            "create" => CreateTransaksi::route("/create"),
            "edit" => EditTransaksi::route("/{record}/edit"),
        ];
    }
}
