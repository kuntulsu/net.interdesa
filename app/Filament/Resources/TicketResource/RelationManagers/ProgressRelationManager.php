<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Illuminate\View\View;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgressRelationManager extends RelationManager
{
    protected static string $relationship = 'progress';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    public function getRelationManagerContent(): View
    {
        return view('welcome', [
            'record' => $this->getOwnerRecord(),
        ]);
    }
    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->recordTitleAttribute('title')
    //         ->columns([
    //             Tables\Columns\TextColumn::make('title'),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->headerActions([
    //             Tables\Actions\CreateAction::make(),
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //             Tables\Actions\DeleteAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }
}
