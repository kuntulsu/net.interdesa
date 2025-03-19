<?php

namespace App\Filament\Clusters\PelangganManager\Resources\ODPResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PelangganRelationManager extends RelationManager
{
    protected static string $relationship = 'pelanggan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('nama')
                //     ->required()
                //     ->maxLength(255),
                Select::make("pelanggan_id")
                    ->options(Pelanggan::selectRaw("id, CONCAT(nama, ' - ', alamat) as nama")->where("odp_id", null)->pluck("nama", "id"))
                    ->searchable()
                    ->label("Link Pelanggan")
                    ->native(false)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("Daftar Pelanggan ODP")
            ->modifyQueryUsing(fn($query) => $query->with("profil.secret.active"))
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('alamat'),
                TextColumn::make("is_active")
                ->getStateUsing(function ($record){
                    $active = $record->profil->secret->active;
                    if($active) {
                        return "active";
                    }else{
                        return "inactive";
                    }
                })->badge()
                ->color(fn($state) => match($state) {
                    "active" => "success",
                    "inactive" => "danger"
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model) {
                        $pelanggan  = Pelanggan::find($data['pelanggan_id']);
                        $pelanggan->odp_id = $this->ownerRecord->id;
                        $pelanggan->save();

                        return $pelanggan;
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function($record) {
                        $record->odp_id = null;
                        $record->save();
                    })
                    ->requiresConfirmation(),
            ]);
    }
}
