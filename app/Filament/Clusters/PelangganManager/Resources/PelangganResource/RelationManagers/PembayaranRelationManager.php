<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers;

use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    return $paket->harga->harga;
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
            ->query(
                Tagihan::query()->with([
                    "terbayar" => fn($query) => $query->where(
                        "pelanggan_id",
                        $this->ownerRecord->id
                    ),
                ])
            )

            ->columns([
                Tables\Columns\TextColumn::make("name")->searchable(),
                Tables\Columns\TextColumn::make("terbayar.nominal_tagihan")
                    ->money("IDR")
                    ->label("Tagihan Terbayar")
                    ->default("-"),
                Tables\Columns\TextColumn::make("operator")->default(function (
                    $record
                ) {
                    return $record->terbayar?->operator?->name;
                }),
                Tables\Columns\TextColumn::make("terbayar.created_at")
                    ->label("Terbayar Pada")
                    ->date("d F Y"),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->using(function (
                    array $data,
                    string $model
                ) {
                    \App\Models\PembayaranPelanggan::create([
                        "pelanggan_id" => $this->ownerRecord->id,
                        "tagihan_id" => $data["tagihan_id"],
                        "user_id" => auth()->user()->id,
                        "nominal_tagihan" => $data["nominal_tagihan"],
                    ]);

                    return $model::find($data["tagihan_id"]);
                }),
            ])
            ->actions([
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
