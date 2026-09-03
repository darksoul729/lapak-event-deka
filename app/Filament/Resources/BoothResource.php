<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoothResource\Pages;
use App\Models\Booth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BoothResource extends Resource
{
    protected static ?string $model = Booth::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Penyelenggara';

    protected static ?string $modelLabel = 'Data Booth';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Lokasi & Status Booth')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->relationship('event', 'nama_event')
                            ->label('Event Bazar')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('kode_booth')
                            ->label('Kode Booth')
                            ->placeholder('Contoh: A-01')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('zona')
                            ->label('Zona')
                            ->options([
                                'Food Court' => 'Zona Food Court / Kuliner',
                                'Reguler' => 'Zona Reguler',
                                'VIP' => 'Zona VIP / Main Stage',
                            ])
                            ->default('Reguler')
                            ->required(),
                        Forms\Components\TextInput::make('ukuran')
                            ->label('Ukuran Booth')
                            ->default('3x3 m')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Booth Khusus (Opsional)')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Kosongkan untuk ikut harga default event'),
                        Forms\Components\Select::make('status')
                            ->label('Status Ketersediaan')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'dipesan' => 'Dipesan / Menunggu Verifikasi',
                                'terisi' => 'Terisi (Sudah Lunas)',
                            ])
                            ->default('tersedia')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.nama_event')
                    ->label('Event Bazar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_booth')
                    ->label('Kode Booth')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('zona')
                    ->label('Zona')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Food Court' => 'warning',
                        'VIP' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('ukuran')
                    ->label('Ukuran'),
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', true)
                    ->placeholder(fn (?Booth $record) => 'Rp ' . number_format($record?->event?->biaya_booth ?? 0, 0, ',', '.')),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'dipesan' => 'warning',
                        'terisi' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tersedia' => 'Tersedia',
                        'dipesan' => 'Dipesan',
                        'terisi' => 'Terisi',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('application.umkm.nama_usaha')
                    ->label('Tenant Penghuni')
                    ->placeholder('- Belum Terisi -')
                    ->icon('heroicon-m-building-storefront')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'nama_event')
                    ->label('Filter Event'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'dipesan' => 'Dipesan',
                        'terisi' => 'Terisi',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn () => Auth::user()?->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => Auth::user()?->isAdmin()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooths::route('/'),
            'create' => Pages\CreateBooth::route('/create'),
            'edit' => Pages\EditBooth::route('/{record}/edit'),
        ];
    }
}
