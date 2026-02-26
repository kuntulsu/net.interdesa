<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Actions\BulkActionGroup;
use Dom\Text;
use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use Filament\Forms\Set;
use App\TipeTagihanEnum;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Actions\StaticAction;
use App\Models\PembayaranPelanggan;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PembayaranRelationManager extends RelationManager
{
    protected static string $relationship = "tagihan";
    protected bool $lunas = false;
    protected static bool $isLazy = false;
    public function isReadOnly(): bool
    {
        return false;
    }
    // public function form(Form $form): Form
    // {
    //     return $form->schema([
    //         Forms\Components\Select::make("tagihan_id")
    //             ->label("Nama Tagihan")
    //             // ->native(false)
    //             ->required()
    //             ->options(function () {
    //                 return Tagihan::whereDoesntHave("terbayar", function (
    //                     $query
    //                 ) {
    //                     $query->where("pelanggan_id", $this->ownerRecord->id);
    //                 })
    //                     ->get()
    //                     ->pluck("name", "id");
    //             }),
    //         Forms\Components\TextInput::make("nama_paket")
    //             ->readOnly()
    //             ->default(function () {
    //                 $secret = $this->ownerRecord->profil->secret;
    //                 $paket = \App\Models\PPPoE\Profile::where(
    //                     "name",
    //                     $secret->profile
    //                 )->first();
    //                 return $secret->profile;
    //             }),
    //         Forms\Components\TextInput::make("nominal_tagihan")->default(
    //             function () {
    //                 $secret = $this->ownerRecord->profil->secret;
    //                 $paket = \App\Models\PPPoE\Profile::where(
    //                     "name",
    //                     $secret->profile
    //                 )->first();
    //                 return $paket->harga->harga ?? 0;
    //             }
    //         ),
    //         Forms\Components\TextInput::make("user.name")
    //             ->label("Operator")
    //             ->readOnly()
    //             ->default(fn() => auth()->user()->name),
    //     ]);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query){
                return $query->orWhere("tipe_tagihan", TipeTagihanEnum::BULANAN);
            })

            ->columns([
                TextColumn::make("name"),
                TextColumn::make("tipe_tagihan"),
                TextColumn::make("nominal_tagihan")
                    ->getStateUsing(function(Tagihan $record){
                        $terbayar = PembayaranPelanggan::where("tagihan_id", $record->id)
                            ->where("pelanggan_id", $this->ownerRecord->id)
                            ->first();
                        $this->lunas = $record->lunas(
                            ($record->tipe_tagihan == TipeTagihanEnum::BULANAN) 
                                ? $this->ownerRecord->id 
                                : null
                        );
                        if($record->tipe_tagihan == TipeTagihanEnum::BULANAN){
                            $pelanggan = $this->ownerRecord;
                            $tagihan = $pelanggan->profil->secret->paket->harga?->harga;
                            if($this->lunas){ 
                                // tampilkan harga yang sudah terbayar untuk menghindari perubahan profile/paket pelanggan
                                return $record->pembayaran()
                                    ->where("pelanggan_id", $pelanggan->id)
                                    ->sum("nominal_tagihan");
                            }
                            return $terbayar?->nominal_tagihan ?? $tagihan;
                        }
                        return $record->nominal_tagihan;
                    })
                    ->money("IDR"),
                IconColumn::make("is_paid")
                    ->default("")
                    ->label("Lunas")
                    ->icon(function($record) {
                        if($this->lunas){
                            return "heroicon-o-check-circle";
                        }
                        return "heroicon-o-x-circle";
                    })
                    ->color(function($record) {
                        if($this->lunas){
                            return "success";
                        }
                        return "warning";    
                    }),
                    // ->iconColor("success")
                // Tables\Columns\TextColumn::make("end_date")
                //     ->label("Jatuh Tempo")
                //     ->date("d F Y"),
                TextColumn::make("operator")
                    ->getStateUsing(function (Tagihan $record){
                        return $record->pembayaran()
                            ->where("pelanggan_id", $this->ownerRecord->id)
                            ->where("tagihan_id", $record->id)
                            ->first()
                            ?->operator?->name;
                    })
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
 
            ->recordActions([
                Action::make("bayar")
                    // ->modalSubmitAction(fn (StaticAction $action) => $action->label('Bayar Tagihan'))
                    ->hidden(fn() => $this->lunas)

                    ->modalContent(fn ($action, $record) => view("components.payment-dialog", ['action' => $action, 'record' => $record]))
                    // ->form([
                    //     TextInput::make("nama_pelanggan")
                    //         ->label("Nama Pelanggan")
                    //         ->readOnly()
                    //         ->default(fn() => $this->ownerRecord->nama),
                    //     TextInput::make("alamat_pelanggan")
                    //         ->label("Alamat Pelanggan")
                    //         ->readOnly()
                    //         ->default(fn() => $this->ownerRecord->alamat),
                    //     TextInput::make("payment_method")
                    //         ->default("hello")
                    //         ->hidden(),
                    //     TextInput::make("paket")
                    //         ->readOnly()
                    //         ->default(fn($record) => $this->ownerRecord->profil->secret->profile)
                    //         ->hidden(fn($record) => ($record->tipe_tagihan != TipeTagihanEnum::BULANAN)),
                    //     TextInput::make("tipe_tagihan")->readOnly(),
                    //     TextInput::make("nominal_tagihan")
                    //         // ->formatState("hello")
                    //         ->default(function ($record) {
                    //             if($record->tipe_tagihan == TipeTagihanEnum::BULANAN){
                    //                 $pelanggan = $this->ownerRecord;
                    //                 $tagihan = $pelanggan->profil->secret->paket->harga?->harga;
                    //                 return $tagihan;
                    //             }
                    //             return $record->nominal_tagihan;
                    //         }),
                    //     TextInput::make("Operator")
                    //         ->readOnly()
                    //         ->default(fn() => auth()->user()->name)
                    // ])
                    // ->action(function($record, $data) {
                    //     dd($data);
                    //     if($record->tipe_tagihan == TipeTagihanEnum::BULANAN){
                    //         $pelanggan = $this->ownerRecord;
                    //         $tagihan = $pelanggan->profil->secret->paket->harga?->harga;
                    //     }else{
                    //         $tagihan = $record->nominal_tagihan;
                    //     }
                        
                    //     $pembayaran = $record->pembayaran()->create([
                    //         "pelanggan_id" => $this->ownerRecord->id,
                    //         "user_id" => auth()->user()->id,
                    //         "nominal_tagihan" => $data['nominal_tagihan'] ?? $tagihan
                    //     ]);

                    //     if($pembayaran) {
                    //         Notification::make("payment_success")
                    //             ->success()
                    //             ->title("Pembayaran Berhasil")
                    //             ->send();
                    //     }

                    // })
                    // ->infolist([
                    //     TextEntry::make("nama_pelanggan")
                    //         ->label("Nama Pelanggan")
                    //         ->default(fn() => $this->ownerRecord->nama),
                    // ])
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->icon("heroicon-o-banknotes")
                    ->button(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

                Action::make("invoice")
                    ->action(fn($record) => dd($record))
                    ->icon("heroicon-o-eye")
                    ->schema([
                        Section::make()
                            ->columns(2)
                            ->schema([
                                TextEntry::make("nama_pelanggan")
                                    ->label("Nama Pelanggan")
                                    ->default(fn() => $this->ownerRecord->nama),
                                TextEntry::make("alamat_pelanggan")
                                    ->label("Alamat Pelanggan")
                                    ->default(fn() => $this->ownerRecord->alamat),
                                TextEntry::make("paket")
                                    ->default(fn($record) => $this->ownerRecord->profil->secret->profile)
                                    ->hidden(fn($record) => ($record->tipe_tagihan != TipeTagihanEnum::BULANAN)),
                                TextEntry::make("tipe_tagihan"),
                                TextEntry::make("nominal tagihan")
                                    // ->formatState("hello")
                                    ->default(function ($record) {
                                        if($record->tipe_tagihan == TipeTagihanEnum::BULANAN){
                                            $pelanggan = $this->ownerRecord;
                                            $tagihan = $pelanggan->profil->secret->paket->harga?->harga;
                                            return $tagihan;
                                        }
                                        return $record->nominal_tagihan;
                                    })
                                    ->color("success")
                                    ->money("IDR"),
                                TextEntry::make("Operator")
                                    ->default(fn() => auth()->user()->name)
                            ]),
                        
                        
                        
                    ])
                    // ->action(function () {
                    //     $this->js("window.print()");
                    // })
                    ->url(function ($record) {
                        $pembayaran = $record->pembayaran()
                            ->where("tagihan_id", $record->id)
                            ->where("pelanggan_id", $this->ownerRecord->id)
                            ->first();
                        if($pembayaran){
                            return route("invoice", ["pembayaran" => $pembayaran->id]);
                        }
                        return null;
                    },shouldOpenInNewTab: true) 
                    ->modalSubmitActionLabel("Print")
                    ->modalCancelAction(false)
                    ->modalCloseButton(false)
                    ->hidden(function ($record) {
                        $pembayaranLunas = $record->pembayaran()->where("pelanggan_id", $this->ownerRecord->id)
                            ->where("tagihan_id", $record->id)
                            ->first();
                        return ($pembayaranLunas) ? false : true;
                    })
                    ->button()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
