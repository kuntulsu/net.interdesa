<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Filament\Clusters\PelangganManager\Resources\PelangganResource\Widgets\ClientMonitor;
use App\Models\Pelanggan;
use App\Models\PPPoE\Secret;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\SubNavigationPosition;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PelangganResource;
use App\Helpers\Helper;
use App\Models\System\Resource;
use Illuminate\Support\Facades\Log;
use Filament\Infolists\Components\ViewEntry;
class ViewPelanggan extends ViewRecord
{
    protected static string $resource = PelangganResource::class;
    protected static ?string $title = "Lihat Pelanggan";

    public function informasi_teknis()
    {
        if(Helper::server_checkup()) {
            $secret = $this->getRecord()->profil->secret;

            return Section::make("Informasi Teknis")
                ->columns(2)
                ->description("Informasi Teknis Pelanggan (Server Side)")
                ->schema([
                    Actions::make([
                        Action::make("refresh_connection")
                            ->requiresConfirmation(function ($action, $record) {
                                $action->modalDescription(
                                    "Akan Merefresh Koneksi PPPoE (Jika Aktif)"
                                );

                                return $action;
                            })
                            ->action(function ($record) {
                                $record->profil->secret->active?->dropConnection();

                                Notification::make()
                                    ->title("Session Refreshed Successfully")

                                    ->success() // Sets the notification type to success
                                    ->send(); // Sends the notification
                            })
                            ->icon("heroicon-o-arrow-path"),
                        Action::make("disable_connection")
                            ->requiresConfirmation(function ($action, $record) {
                                $action->modalDescription(
                                    "Akan Mengisolir Koneksi PPPoE"
                                );
                                return $action;
                            })
                            ->action(function (Pelanggan $record){
                                $secret = $record->profil->secret;
                                $disable = $secret?->disable();
                                $secret?->active?->dropConnection();
                                if($disable) {
                                    Notification::make()
                                        ->title("Secret Disabled Successfully")
                                        ->success()
                                        ->send();
                                    return true;
                                }
                            })
                            ->color("danger")
                            ->icon("heroicon-o-power"),
                    ]),
                    TextEntry::make("sec_name")
                        ->getStateUsing(function ($record) use ($secret){
                            return $secret->name;
                        })
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Username"),
                    TextEntry::make("sec_password")
                        ->getStateUsing(function ($record) use ($secret){
                            return $secret->password;
                        })
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Password"),
                    TextEntry::make("sec_local-address")
                        ->getStateUsing(function ($record) use ($secret){
                            return $secret['local-address'];
                        })
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Local Address"),
                    TextEntry::make("sec_remote-address")
                        ->getStateUsing(function ($record) use ($secret){
                            return $secret['remote-address'];
                        })
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Remote Address"),
                    TextEntry::make("sec_profile")
                        ->getStateUsing(function ($record) use ($secret){
                            return $secret->profile;
                        })
                        ->label("Profile Paket"),
                    TextEntry::make("sec_service")
                    ->getStateUsing(function ($record) use ($secret){
                        return $secret->service;
                    })->label("Tipe Layanan"),
                ])
                ->collapsible();
        }else{
            return ViewEntry::make('status')
                ->view("livewire.server-info");
        }
    }
    public function getHeaderWidgets(): array
    {
        return  [
            ClientMonitor::make()
        ];
    }
    public function getWidgetData(): array
    {
        return [
            "record" => $this->record
        ];
    }
    public function infolist(Schema $schema): Schema
    {
        return $infolist->schema([
            
            Section::make("Informasi Pribadi")
                ->columns(2)
                ->description(
                    "Informasi Pribadi Pelanggan Internet Desa Pecangaan Kulon"
                )
                ->schema([
                    ViewEntry::make('testing')
                        ->hidden(fn($record) => !$record->whitelist)
                        ->columnSpanFull()
                        ->view("livewire.is-whitelisted"),
                    Actions::make([
                        Action::make("whitelist pelanggan")
                            ->hidden(fn($record) => $record->whitelist)
                            ->label("Whitelist Pelanggan")
                            ->color("success")
                            ->icon("heroicon-o-arrow-path")
                            ->requiresConfirmation(function ($action) {
                                $action->modalDescription(
                                    "Akan Whitelist Pelanggan dan Pelanggan Tidak Akan Terkena Isolir pada saat waktu Pengisoliran"
                                );

                                return $action;
                            })
                            ->action(function ($record) {
                                $record->whitelist = true;
                                $record->save();

                                Notification::make()
                                    ->title("Pelanggan Berhasil ter-whitelist")

                                    ->success() // Sets the notification type to success
                                    ->send(); // Sends the notification
                        }),
                        Action::make("unwhitelist pelanggan")
                            ->hidden(fn($record) => !$record->whitelist)
                            ->label("un-Whitelist Pelanggan")
                            ->color("warning")
                            ->icon("heroicon-o-arrow-path")
                            ->requiresConfirmation(function ($action) {
                                $action->modalDescription(
                                    "Akan Menghapus Whitelist Pelanggan dan Pelanggan Akan Terkena Isolir pada saat waktu Pengisoliran"
                                );

                                return $action;
                            })
                            ->action(function ($record) {
                                $record->whitelist = false;
                                $record->save();

                                Notification::make()
                                    ->title("Pelanggan Berhasil di-unwhitelist")

                                    ->success() // Sets the notification type to success
                                    ->send(); // Sends the notification
                        }),
                    ]),
                    TextEntry::make("nama"),
                    TextEntry::make("alamat"),
                    TextEntry::make("telp")->prefix("+62"),
                    TextEntry::make("jatuh_tempo")->date("d F Y"),
                ])
                ->collapsible(),

            $this->informasi_teknis()
        ]);
    }
}
