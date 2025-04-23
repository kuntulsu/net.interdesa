<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Filament\Resources\TicketResource\RelationManagers\ProgressRelationManager;
use App\Models\Ticket;
use App\Models\TicketProgress;
use App\TicketStatus;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->label('Title'),
                TextInput::make('description')
                    ->required()
                    ->label('Description')
                    ->maxLength(65535),
                Select::make('pelanggan_id')
                    ->options(
                            \App\Models\Pelanggan::selectRaw("id, CONCAT(nama, ' - ', alamat) as nama")->pluck("nama", "id")
                        )
                    ->searchable()
                    ->label('Pelanggan')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("title")
                    ->description(fn (Ticket $record): string => $record->pelanggan->nama),
                TextColumn::make("status")
                    ->icon(fn($record) => match($record->status){
                        TicketStatus::Waiting => "heroicon-o-clock",
                        TicketStatus::Process => "heroicon-o-wrench-screwdriver",
                        TicketStatus::Completed => "heroicon-o-check-circle",
                    })
                    ->color(fn($record) => match($record->status){
                        TicketStatus::Waiting => "warning",
                        TicketStatus::Process => "primary",
                        TicketStatus::Completed => "success",
                    })
                    ->badge(),

                TextColumn::make("user.name")
                    ->label("Issuer"),
                TextColumn::make("solved_by.name")
                    ->label("Petugas"),
                TextColumn::make("created_at")
                    ->label("Created At")
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ]);
    }

    // public static function getRelations(): array
    // {
    //     return [
    //         ProgressRelationManager::class
    //     ];
    // }
    public static function getRecordRouteKeyName(): ?string
    {
        return 'uuid';
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
