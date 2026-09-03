<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UmkmResource\Pages;
use App\Models\Umkm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UmkmResource extends Resource
{
    protected static ?string $model = Umkm::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Manajemen UMKM';

    protected static ?string $modelLabel = 'Profil UMKM';

    protected static ?string $pluralModelLabel = 'Profil UMKM';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()?->isTenant()) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Usaha')
                    ->description('Lengkapi data profil usaha UMKM Anda.')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),
                        Forms\Components\TextInput::make('nama_usaha')
                            ->label('Nama Usaha')
                            ->placeholder('Contoh: Kopi Kenangan Samarinda')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_pemilik')
                            ->label('Nama Pemilik')
                            ->placeholder('Contoh: Budi Santoso')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nomor_whatsapp')
                            ->label('Nomor WhatsApp')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Select::make('kategori_usaha')
                            ->label('Kategori Usaha')
                            ->options([
                                'Kuliner' => 'Kuliner (Makanan & Minuman)',
                                'Fashion' => 'Fashion & Pakaian',
                                'Kriya' => 'Kriya & Kerajinan Tangan',
                                'Jasa' => 'Jasa & Pelayanan',
                                'Elektronik' => 'Elektronik & Gadget',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram Usaha')
                            ->placeholder('Contoh: @umkm_samarinda')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo / Foto Produk')
                            ->image()
                            ->directory('umkm-logos')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Usaha')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('deskripsi_produk')
                            ->label('Deskripsi Produk / Layanan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('nama_usaha')
                    ->label('Nama Usaha')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nama_pemilik')
                    ->label('Pemilik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori_usaha')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kuliner' => 'warning',
                        'Fashion' => 'success',
                        'Kriya' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nomor_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('instagram')
                    ->label('Instagram')
                    ->icon('heroicon-m-camera'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_usaha')
                    ->options([
                        'Kuliner' => 'Kuliner',
                        'Fashion' => 'Fashion',
                        'Kriya' => 'Kriya',
                        'Jasa' => 'Jasa',
                        'Elektronik' => 'Elektronik',
                        'Lainnya' => 'Lainnya',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUmkms::route('/'),
            'create' => Pages\CreateUmkm::route('/create'),
            'edit' => Pages\EditUmkm::route('/{record}/edit'),
        ];
    }
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUmkms::route('/'),
            'create' => Pages\CreateUmkm::route('/create'),
            'edit' => Pages\EditUmkm::route('/{record}/edit'),
        ];
    }
}
