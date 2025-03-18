<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers\OdpRelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;

use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PelangganResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers\PembayaranRelationManager;
use App\Filament\Resources\PelangganResource\Widgets\PelangganOverview;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;
    // protected static ?string $cluster = \App\Filament\Clusters\PelangganManager::class;
    // protected static ?string $recordTitleAttribute = "pelanggan";

    protected static ?string $slug = "pelanggan";
    protected static ?string $navigationIcon = "heroicon-o-user-group";
    protected static ?string $navigationGroup = 'Pelanggan Manager';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make("Informasi Pribadi")
                ->columns(2)
                ->schema([
                    TextInput::make("nama")->required(),
                    TextInput::make("nik")
                        ->label("NIK")
                        ->numeric()
                        ->inputMode("decimal")
                        ->default("0000000000000000")
                        ->length(16)
                        ->required(),
                    TextInput::make("alamat")->label("Alamat")->required(),
                    TextInput::make("telp")
                        ->label("Nomor Telepon")
                        ->prefix("+62")
                        ->default("000000000000")
                        ->numeric()
                        ->inputMode("decimal")
                        ->required(),
                    DatePicker::make("jatuh_tempo")
                        ->label("Jatuh Tempo")
                        ->native(false)
                        ->default(\Carbon\Carbon::create("15-11-2024"))
                        ->required(),
                    Select::make("secret_id")
                        ->label("Connect to PPPoE Secret")
                        ->relationship("profil")
                        // ->hiddenOn("edit")
                        ->hidden(function ($record) {
                            return $record?->profil ? true : false;
                        })
                        ->preload()
                        ->searchable()
                        ->helperText(
                            "Hanya Secret Yang Belum Terdaftar di Pelanggan"
                        )
                        ->options(
                            \App\Models\PPPoE\Secret::with("profil")
                                ->get()
                                ->where("profil", null)
                                ->pluck("name", "id")
                        )
                        ->createOptionForm([
                            Section::make("Informasi Teknis")
                                ->columns(2)
                                ->schema([
                                    Hidden::make("secret.id"),

                                    TextInput::make("secret.name")->label(
                                        "PPPoE Username"
                                    ),
                                    TextInput::make("secret.password")
                                        ->label("PPPoE Password")
                                        ->password()
                                        ->revealable(),
                                    Select::make("secret.profile")
                                        ->options(
                                            \App\Models\PPPoE\Profile::all()->pluck(
                                                "name",
                                                "name"
                                            )
                                        )
                                        ->label("Paket")
                                        ->native(false),
                                    TextInput::make("secret.local-address")
                                        ->label("Local Address")
                                        ->ipv4(),
                                    TextInput::make("secret.remote-address")
                                        ->label("Remote Address")
                                        ->ipv4(),
                                ]),
                        ])
                        ->createOptionUsing(function (array $data): string|null {
                            $secret = \App\Models\PPPoE\Secret::create(
                                $data["secret"]
                            );
                            if($secret) {
                                Notification::make()
                                    ->title('Saved successfully')
                                    ->success()
                                    ->send();
                            }
                            return $secret?->getKey();
                        }),
                ]),
            Section::make("Informasi Teknis")
                ->columns(2)
                ->hidden(function ($record) {
                    return $record?->profil ? false : true;
                })
                ->schema([
                    Hidden::make("secret.id"),

                    TextInput::make("secret.name")->label("PPPoE Username"),
                    TextInput::make("secret.password")
                        ->label("PPPoE Password")
                        ->password()
                        ->revealable(),
                    Select::make("secret.profile")
                        ->options(
                            \App\Models\PPPoE\Profile::all()->pluck(
                                "name",
                                "name"
                            )
                        )
                        ->label("Paket")
                        ->native(false),
                    TextInput::make("secret.local-address")
                        ->label("Local Address")
                        ->ipv4(),
                    TextInput::make("secret.remote-address")
                        ->label("Remote Address")
                        ->ipv4(),
                ]),
        ]);
    }
    public static function getWidgets(): array
    {
        return [
            PelangganOverview::class
        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPelanggan::class,
            Pages\EditPelanggan::class,
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(function(Builder $query) {
            //     $query->with(["profil" => function ($query){
            //         $query->with("secret");
            //     }]);
            // })
            ->recordClasses(fn(Pelanggan $record) => match($record->whitelist){
                true => "dark:bg-indigo-900 dark:text-white bg-indigo-200 text-indigo-900",
                false => null,
                default => null
            })
            ->columns([

                TextColumn::make("nama")->searchable(),
                TextColumn::make("alamat")->searchable()
                    // ->getStateUsing(function ($record){
                    //     return $record->profil?->secret?->name;
                    // })
                    // ->icon(function (Pelanggan $record) {
                    //     $secret = $record->profil?->secret;
                    //     if ($secret?->disabled) {
                    //         return "heroicon-o-x-circle";
                    //     }
                    //     return $secret?->active
                    //         ? "heroicon-o-arrows-up-down"
                    //         : "heroicon-o-link-slash";
                    // })
                    ->iconColor("primary"),
                TextColumn::make("jatuh_tempo")->date("d F Y"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PembayaranRelationManager::class,
            OdpRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListPelanggans::route("/"),
            "create" => Pages\CreatePelanggan::route("/create"),
            "view" => Pages\ViewPelanggan::route("/{record}"),
            "edit" => Pages\EditPelanggan::route("/{record}/edit"),
        ];
    }
}
