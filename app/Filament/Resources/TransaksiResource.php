<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
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
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = "heroicon-o-arrows-up-down";

    public static function form(Form $form): Form
    {
        return $form->columns(2)->schema([
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
                    fn(\App\TipeTransaksi $state): string => match (
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
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListTransaksis::route("/"),
            "create" => Pages\CreateTransaksi::route("/create"),
            "edit" => Pages\EditTransaksi::route("/{record}/edit"),
        ];
    }
}
