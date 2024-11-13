<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
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
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\RelationManagers;
use App\Filament\Clusters\PelangganManager\Resources\PembayaranPelangganResource\Widgets\PaymentPerUserOverview;

class PembayaranPelangganResource extends Resource
{
    protected static ?string $model = PembayaranPelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $cluster = PelangganManager::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                TextColumn::make("pelanggan_name")
                    ->label("Nama Pelanggan")
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->pelanggan->nama;
                    }),
                TextColumn::make("nominal_tagihan")
                    ->money("IDR"),
                TextColumn::make("operator_name")
                    ->searchable()
                    ->label("Operator")
                    ->getStateUsing(function ($record){
                        return $record->operator->name;
                    }),
                
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label("Operator")
                    ->options(fn (): array => User::query()->pluck('name', 'id')->all()),
                Filter::make('created_at')
                    ->form([
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
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListPembayaranPelanggans::route('/'),
            'create' => Pages\CreatePembayaranPelanggan::route('/create'),
            'edit' => Pages\EditPembayaranPelanggan::route('/{record}/edit'),
        ];
    }
}
