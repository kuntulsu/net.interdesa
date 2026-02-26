<?php

namespace App\Filament\Clusters\PelangganManager\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages\ListTagihans;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages\CreateTagihan;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages\EditTagihan;
use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use App\TipeTagihanEnum;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\PelangganManager;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\Pages;
use App\Filament\Clusters\PelangganManager\Resources\TagihanResource\RelationManagers;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;

    protected static string | \BackedEnum | null $navigationIcon = "heroicon-o-clipboard-document-list";

    // protected static ?string $cluster = PelangganManager::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Pelanggan Manager';


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("name")->label("Nama Tagihan"),
            Select::make("tipe_tagihan")
                ->options(TipeTagihanEnum::toArray())
                ->live()
                ->native(false),
            Select::make("pelanggan_id")
                ->label("Tagihan Kepada")
                ->hidden(function (Get $get) {
                    if(
                        $get("tipe_tagihan") == TipeTagihanEnum::from("Biaya Pemasangan Baru")?->name || $get("tipe_tagihan") == TipeTagihanEnum::from("Lain Lain")?->name
                    ){
                        
                        return false;
                    }
                    return true;
                })
                ->options(Pelanggan::all()->pluck("nama", "id"))
                ->native(false)
                // ->helperText("Kosongkan untuk buat tagihan ke semua pelanggan")
                ->searchable(),
            TextInput::make("nominal_tagihan")
                ->label("Nominal Tagihan")
                ->hidden(function (Get $get) {
                    if(
                        $get("tipe_tagihan") == TipeTagihanEnum::from("Biaya Pemasangan Baru")?->name || $get("tipe_tagihan") == TipeTagihanEnum::from("Lain Lain")?->name
                    ){
                        
                        return false;
                    }
                    return true;
                })
                ->numeric()
                ->inputMode("decimal"),
            DatePicker::make("end_date")->native(false)->label("Jatuh Tempo Tagihan"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(
            //     fn($query) => $query->withSum("terbayar", "nominal_tagihan")
            // )
            ->columns([
                TextColumn::make("name")->searchable(),
                TextColumn::make("tipe_tagihan"),
                TextColumn::make("nominal_tagihan")->money("IDR"),
                IconColumn::make("is_paid")
                    ->default("")
                    ->label("Lunas")
                    ->icon(function($record) {
                        if($record->lunas()){
                            return "heroicon-o-check-circle";
                        }
                        return "heroicon-o-x-circle";
                    })
                    ->color(function($record) {
                        if($record->lunas()){
                            return "success";
                        }
                        return "warning";    
                    }),
                    // ->iconColor("success")
                // Tables\Columns\TextColumn::make("end_date")
                //     ->label("Jatuh Tempo")
                //     ->date("d F Y"),
                TextColumn::make("pembayaran.operator.name")
                    ->default("-")
            ])
            ->filters([
                //
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => ListTagihans::route("/"),
            "create" => CreateTagihan::route("/create"),
            "edit" => EditTagihan::route("/{record}/edit"),
        ];
    }
}
