<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use App\TipeTagihanEnum;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PembayaranRelationManager extends RelationManager
{
    protected static string $relationship = "tagihan";

    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make("tagihan_id")
                ->label("Nama Tagihan")
                // ->native(false)
                ->required()
                ->options(function () {
                    return Tagihan::whereDoesntHave("terbayar", function (
                        $query
                    ) {
                        $query->where("pelanggan_id", $this->ownerRecord->id);
                    })
                        ->get()
                        ->pluck("name", "id");
                }),
            Forms\Components\TextInput::make("nama_paket")
                ->readOnly()
                ->default(function () {
                    $secret = $this->ownerRecord->profil->secret;
                    $paket = \App\Models\PPPoE\Profile::where(
                        "name",
                        $secret->profile
                    )->first();
                    return $secret->profile;
                }),
            Forms\Components\TextInput::make("nominal_tagihan")->default(
                function () {
                    $secret = $this->ownerRecord->profil->secret;
                    $paket = \App\Models\PPPoE\Profile::where(
                        "name",
                        $secret->profile
                    )->first();
                    return $paket->harga->harga ?? 0;
                }
            ),
            Forms\Components\TextInput::make("user.name")
                ->label("Operator")
                ->readOnly()
                ->default(fn() => auth()->user()->name),
        ]);
    }

    public function table(Table $table): Table
    {

        return $table


            ->columns([
                Tables\Columns\TextColumn::make("name")->searchable(),
                Tables\Columns\TextColumn::make("tipe_tagihan"),
                Tables\Columns\TextColumn::make("nominal_tagihan")->money("IDR"),
                IconColumn::make("is_paid")
                    ->default("")
                    ->label("Lunas")
                    ->icon(function($record) {
                        if($record->lunas()){
                            return "heroicon-o-check-circle";
                        }
                        return "heroicon-o-x-circle";
                    })
                    ->color(function($record) {
                        if($record->lunas()){
                            return "success";
                        }
                        return "warning";    
                    }),
                    // ->iconColor("success")
                // Tables\Columns\TextColumn::make("end_date")
                //     ->label("Jatuh Tempo")
                //     ->date("d F Y"),
                TextColumn::make("pembayaran.operator.name")
                    ->default("-")
                // Tables\Columns\TextColumn::make("terbayar.nominal_tagihan")
                //     ->money("IDR")
                //     ->label("Tagihan Terbayar")
                //     ->default("-"),
                // Tables\Columns\TextColumn::make("operator")->default(function (
                //     $record
                // ) {
                //     return $record->terbayar?->operator?->name;
                // }),
                // Tables\Columns\TextColumn::make("terbayar.created_at")
                //     ->label("Terbayar Pada")
                //     ->date("d F Y"),
            ])
            ->filters([
                //
            ])
 
            ->actions([
                Action::make("bayar")
                    ->modalSubmitAction(fn (StaticAction $action) => $action->label('Bayar Tagihan'))
                    ->hidden(fn($record) => $record->lunas())
                    ->action(function($record) {
                        $pembayaran = $record->pembayaran()->create([
                            "pelanggan_id" => $this->getOwnerRecord()->id,
                            "user_id" => auth()->user()->id,
                            "nominal_tagihan" => $record->nominal_tagihan
                        ]);

                        if($pembayaran) {
                            Notification::make("payment_success")
                                ->success()
                                ->title("Pembayaran Berhasil")
                                ->send();
                        }

                    })
                    ->infolist([
                        Section::make()
                            ->columns(2)
                            ->schema([
                                TextEntry::make("nama_pelanggan")
                                    ->label("Nama Pelanggan")
                                    ->default(fn() => $this->getOwnerRecord()->nama),
                                TextEntry::make("alamat_pelanggan")
                                    ->label("Alamat Pelanggan")
                                    ->default(fn() => $this->getOwnerRecord()->alamat),
                                TextEntry::make("paket")
                                    ->default(fn($record) => $this->getOwnerRecord()->profil->secret->profile)
                                    ->hidden(fn($record) => ($record->tipe_tagihan != TipeTagihanEnum::BULANAN)),
                                TextEntry::make("tipe_tagihan"),
                                TextEntry::make("nominal_tagihan")
                                    ->money("IDR"),
                                TextEntry::make("Operator")
                                    ->default(fn() => auth()->user()->name)
                            ]),
                        
                        
                        
                    ])
                    // ->form([
                    //     TextInput::make("nama_pelanggan")
                    //         ->label("Nama Pelanggan")
                    //         ->default(fn() => $this->getOwnerRecord()->nama)
                    //         ->readOnly(),
                    //     TextInput::make("paket")
                    //         ->default(fn($record) => $this->getOwnerRecord()->profil->secret->profile)
                    //         ->hidden(fn($record) => ($record->tipe_tagihan != TipeTagihanEnum::BULANAN))
                    //         ->readOnly()
                    // ])
                    ->icon("heroicon-o-banknotes")
                    ->button()
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
