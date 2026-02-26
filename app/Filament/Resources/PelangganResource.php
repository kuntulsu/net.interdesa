<?php

namespace App\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Carbon\Carbon;
use App\Models\PPPoE\Secret;
use App\Models\PPPoE\Profile;
use App\Filament\Resources\PelangganResource\Pages\ViewPelanggan;
use App\Filament\Resources\PelangganResource\Pages\EditPelanggan;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PelangganResource\Pages\ListPelanggans;
use App\Filament\Resources\PelangganResource\Pages\CreatePelanggan;
use App\Filament\Clusters\PelangganManager\Resources\PelangganResource\RelationManagers\OdpRelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;

use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
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
    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;
    // protected static ?string $cluster = \App\Filament\Clusters\PelangganManager::class;
    // protected static ?string $recordTitleAttribute = "pelanggan";

    protected static ?string $slug = "pelanggan";
    protected static string | \BackedEnum | null $navigationIcon = "heroicon-o-user-group";
    protected static string | \UnitEnum | null $navigationGroup = 'Pelanggan Manager';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
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
                        ->default(Carbon::create("15-11-2024"))
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
                            Secret::with("profil")
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
                                            Profile::all()->pluck(
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
                            $secret = Secret::create(
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
                            Profile::all()->pluck(
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
            ViewPelanggan::class,
            EditPelanggan::class,
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            "index" => ListPelanggans::route("/"),
            "create" => CreatePelanggan::route("/create"),
            "view" => ViewPelanggan::route("/{record}"),
            "edit" => EditPelanggan::route("/{record}/edit"),
        ];
    }
}
