<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use Filament\Schemas\Schema;
use App\TipeTagihanEnum;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages\ListPembayaranPelanggans;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages\CreatePembayaranPelanggan;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages\EditPembayaranPelanggan;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Tagihan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PembayaranPelanggan;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\PelangganManager;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\RelationManagers;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Widgets\PaymentPerUserOverview;
use Filament\Tables\Columns\Summarizers\Sum;

class PembayaranPelangganResource extends Resource
{
    protected static ?string $model = PembayaranPelanggan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    // protected static ?string $cluster = PelangganManager::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Pelanggan Manager';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
    public static function getWidgets(): array
    {
        return [
            PaymentPerUserOverview::class
        ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query){
                return $query->with(["operator", "tagihan", "pelanggan"])
                ->orderBy("created_at", "DESC");
        })
        ->columns([
                TextColumn::make("created_at")
                    ->label("Waktu Pembayaran")
                    ->date("d-F-Y H:i:s"),
                TextColumn::make("pelanggan.nama")
                    ->label("Nama Pelanggan")
                    ->searchable()
                    // ->getStateUsing(function ($record) {
                    //     return $record->pelanggan->nama;
                    // })
                    ,
                TextColumn::make("nominal_tagihan")
                    ->summarize(Sum::make()->label("Total"))
                    ->money("IDR"),
                TextColumn::make("payment_method")
                    ->icon(fn ($record) => match($record->payment_method) {
                        "Cash" => "heroicon-o-banknotes",
                        "Transfer" => "heroicon-o-credit-card",
                        default => "heroicon-o-banknotes",
                    })
                    ->badge()
                    ->color(fn ($record) => match($record->payment_method) {
                        "Cash" => "success",
                        "Transfer" => "info",
                        default => "success",
                    })
                    ->label("Metode Pembayaran"),
                TextColumn::make("operator.name")
                    ->label("Operator")
                    // ->getStateUsing(function ($record){
                    //     return $record->operator->name;
                    // }),
                
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label("Operator")
                    ->options(fn (): array => User::query()->pluck('name', 'id')->all()),
                SelectFilter::make("tagihan_id")
                    ->label("Nama Tagihan")
                    ->options(fn (): array => Tagihan::where("tipe_tagihan", TipeTagihanEnum::to("BULANAN"))->get()->pluck("name", "id")->toArray()),
                SelectFilter::make("payment_method")
                    ->label("Metode Pembayaran")
                    ->options([
                        "0" => "Cash",
                        "1" => "Transfer"
                    ]),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                // Tables\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => ListPembayaranPelanggans::route('/'),
            'create' => CreatePembayaranPelanggan::route('/create'),
            'edit' => EditPembayaranPelanggan::route('/{record}/edit'),
        ];
    }
}
