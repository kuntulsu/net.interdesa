<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use App\Models\Pelanggan;
use App\Models\PPPoE\Secret;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\SubNavigationPosition;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PelangganResource;
use Filament\Infolists\Components\Actions\Action;

class ViewPelanggan extends ViewRecord
{
    protected static string $resource = PelangganResource::class;
    protected static ?string $title = "Lihat Pelanggan";

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make("Informasi Pribadi")
                ->columns(2)
                ->description(
                    "Informasi Pribadi Pelanggan Internet Desa Pecangaan Kulon"
                )
                ->schema([
                    TextEntry::make("nama"),
                    TextEntry::make("alamat"),
                    TextEntry::make("telp")->prefix("+62"),
                    TextEntry::make("jatuh_tempo")->date("d F Y"),
                ])
                ->collapsible(),

            Section::make("Informasi Teknis")
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
                    TextEntry::make("profil.secret.name")
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Username"),
                    TextEntry::make("profil.secret.password")
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Password"),
                    TextEntry::make("profil.secret.local-address")
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Local Address"),
                    TextEntry::make("profil.secret.remote-address")
                        ->copyable()
                        ->copyMessage("Copied.")
                        ->label("PPPoE Remote Address"),
                    TextEntry::make("profil.secret.profile")->label(
                        "Profile Paket"
                    ),
                    TextEntry::make("profil.secret.service")->label(
                        "Tipe Layanan"
                    ),
                ])
                ->collapsible(),
        ]);
    }
}
