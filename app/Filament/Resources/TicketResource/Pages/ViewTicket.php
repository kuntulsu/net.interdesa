<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Dom\Text;
use App\TicketStatus;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Resources\TicketResource;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ViewTicket extends Page implements HasInfolists,HasForms
{
    use InteractsWithRecord;
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ticket-resource.pages.view-ticket';
    public $data = [];
    public function createAction()
    {
        return Action::make("create")
            ->label("Make Progress")
            ->hidden(fn() => ($this->record->status == \App\TicketStatus::Completed))
            ->action(function ($data) {
                $this->record->progress()->create([
                    "task" => $data["task"],
                    "is_solved" => $data["is_solved"]
                ]);

                $this->record->update([
                    "status" => \App\TicketStatus::Process
                ]);

                if($data["is_solved"] == 1) {
                    $this->record->update([
                        "status" => \App\TicketStatus::Completed,
                        "solver" => auth()->id()
                    ]);
                }
            })
            ->icon("heroicon-o-pencil-square")
            ->size(ActionSize::Small)
            ->form([
                TextInput::make("task")
                    ->required()
                    ->maxLength(255),
                Select::make("is_solved")
                    ->label("Apakah dengan Task Ini Masalah Telah Selesai?")
                    ->options([
                        0 => "Belum Selesai",
                        1 => "Selesai"
                    ])
                    ->default(0)
            ]);

    }
    public function productInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->columns(2)
            ->schema([
                TextEntry::make("title"),
                TextEntry::make("description"),
                TextEntry::make("status")
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
                TextEntry::make("pelanggan.nama")
                    ->label("Nama Pelanggan"),
                TextEntry::make("pelanggan.alamat")
                    ->label("Alamat Pelanggan"),
                TextEntry::make("created_at")
                    ->label("Created At")
                    ->dateTime()
            ]);
    }
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
