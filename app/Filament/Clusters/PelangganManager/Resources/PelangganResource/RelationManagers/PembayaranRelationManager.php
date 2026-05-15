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
use Illuminate\Support\Facades\DB;

class PembayaranRelationManager extends RelationManager
{
    protected static string $relationship = "tagihan";
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
                    
                return $query
                    ->with(["pembayaran" => fn($q) => $q->with("operator")->where("pelanggan_id", $this->ownerRecord->id)])
                    ->orWhere("tipe_tagihan", TipeTagihanEnum::BULANAN)->orderBy("created_at", "desc");
            })

            ->columns([
                TextColumn::make("name"),
                TextColumn::make("tipe_tagihan"),

                TextColumn::make("nominal_tagihan")
                    ->getStateUsing(function(Tagihan $record){
                        $terbayar = PembayaranPelanggan::where("tagihan_id", $record->id)
                            ->where("pelanggan_id", $this->ownerRecord->id)
                            ->first();
                        if($record->tipe_tagihan == TipeTagihanEnum::BULANAN){
                            $pelanggan = $this->ownerRecord;
                            $tagihan = $pelanggan->profil->secret->paket->harga?->harga;
                            if($this->check_lunas($tagihan, $record->pembayaran->sum("nominal_tagihan"))){ 
                                // tampilkan harga yang sudah terbayar untuk menghindari perubahan profile/paket pelanggan
                                return $record->pembayaran
                                    ->where("pelanggan_id", $pelanggan->id)
                                    ->sum("nominal_tagihan");
                            }
                            return $terbayar?->nominal_tagihan ?? $tagihan;
                        }
                        return $record->nominal_tagihan;
                    })
                    ->money("IDR"),
                IconColumn::make("status")
                    ->label("Lunas")
                    ->getStateUsing(fn ($record) => $this->check_lunas($record->nominal_tagihan, $record->pembayaran->sum("nominal_tagihan")))
                    ->icon(fn($record) => $this->check_lunas($record->nominal_tagihan, $record->pembayaran->sum("nominal_tagihan")) ? "heroicon-o-check-circle" : "heroicon-o-x-circle")
                    ->color(fn($record) => $this->check_lunas($record->nominal_tagihan, $record->pembayaran->sum("nominal_tagihan")) ? "success" : "danger"),
                // Tables\Columns\TextColumn::make("end_date")
                //     ->label("Jatuh Tempo")
                //     ->date("d F Y"),
                TextColumn::make("operator")
                    ->label("Operator")
                    ->getStateUsing(function (Tagihan $record){
                        return $record->pembayaran
                            ->where("pelanggan_id", $this->ownerRecord->id)
                            ->where("tagihan_id", $record->id)
                            ->first()
                            ?->operator?->name;
                    })
                    ->default("")
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
                    ->hidden(fn($record) => $this->check_lunas($record->nominal_tagihan, $record->pembayaran->sum("nominal_tagihan")))
                    ->modalContent(fn ($action, $record) => view("components.payment-dialog", ['action' => $action, 'record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->icon("heroicon-o-banknotes")
                    ->button(),

                Action::make("invoice")
                    ->icon("heroicon-o-eye")
                    ->hidden(fn($record) => !$this->check_lunas(
                        $record->nominal_tagihan ?? $this->ownerRecord->pelanggan->profil->secret->paket->harga?->harga,
                        $record->pembayaran->sum("nominal_tagihan")
                    ))
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
                    ->url(function ($record) {
                        $pembayaran = $record->pembayaran
                            ->first();
                        if($pembayaran){
                            return route("invoice", ["pembayaran" => $pembayaran->id]);
                        }
                        return null;
                    },shouldOpenInNewTab: true) 
                    ->modalSubmitActionLabel("Print")
                    ->modalCancelAction(false)
                    ->modalCloseButton(false)
                    ->color("success")
                    ->button()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function check_lunas(int $tagihan, int $dibayar): bool
    {
        if ($tagihan == 0) {
            $tagihan = $this->ownerRecord->profil->secret->paket->harga?->harga ?? 0;
        }
        return (float)$dibayar >= (float)$tagihan;
    }
}
