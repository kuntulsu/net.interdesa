<?php

namespace App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Tables;
use \App\Models\Pelanggan;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ActionGroup;

class OdpRelationManager extends RelationManager
{
    protected static string $relationship = 'odp';
    protected static ?string $title = "Pelanggan se-ODP";
 
    public function isReadOnly(): bool
    {
        return true;
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    public function getTableQuery(): Builder
    {
        $odp_id = $this->ownerRecord->odp?->id;
        if(!$odp_id){
            return Pelanggan::where("odp_id", "HolderToNull");
        }
        return Pelanggan::where("odp_id", $odp_id);
    }
    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make("maps")
                    ->label("Lokasi ODP")
                    ->url(fn() => "https://maps.google.com/?q=".$this->ownerRecord->odp?->coordinate, shouldOpenInNewTab:true)
                    ->link()
                    ->icon("heroicon-o-map-pin")
            ])
            ->heading("ODP: {$this->ownerRecord->odp?->nama} - {$this->ownerRecord->odp?->description}")
            ->emptyStateHeading("Pelanggan Tidak Terdaftar di ODP") 
            ->recordTitleAttribute('nama')
            ->modifyQueryUsing(fn($query) => $query->with("profil.secret.active"))
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('alamat'),
                TextColumn::make("is_active")
                ->getStateUsing(function ($record){
                    $active = $record->profil->secret->active;
                    if($active) {
                        return "active";
                    }else{
                        return "inactive";
                    }
                })
                ->color(fn($state) => match($state) {
                    "active" => "success",
                    "inactive" => "danger"
                })
                ->badge()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
