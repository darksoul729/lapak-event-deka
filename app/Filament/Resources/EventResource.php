<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Penyelenggara';

    protected static ?string $modelLabel = 'Event Bazar';

    protected static ?string $pluralModelLabel = 'Event Bazar';

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
                Forms\Components\Section::make('Informasi Utama Event')
                    ->schema([
                        Forms\Components\TextInput::make('nama_event')
                            ->label('Nama Event Bazar')
                            ->placeholder('Contoh: Samarinda Culinary Market 2026')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi Acara')
                            ->placeholder('Contoh: GOR Segiri Samarinda')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tanggal_pelaksanaan')
                            ->label('Tanggal Pelaksanaan')
                            ->required(),
                        Forms\Components\DateTimePicker::make('batas_pendaftaran')
                            ->label('Batas Pendaftaran')
                            ->required(),
                        Forms\Components\TextInput::make('kuota_tenant')
                            ->label('Kuota Tenant')
                            ->numeric()
                            ->default(30)
                            ->required(),
                        Forms\Components\TextInput::make('biaya_booth')
                            ->label('Biaya Booth (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(1500000)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status Event')
                            ->options([
                                'draft' => 'Draft (Belum Dipublikasi)',
                                'pendaftaran_dibuka' => 'Pendaftaran Dibuka',
                                'pendaftaran_ditutup' => 'Pendaftaran Ditutup',
                                'selesai' => 'Selesai',
                            ])
                            ->default('pendaftaran_dibuka')
                            ->required(),
                        Forms\Components\FileUpload::make('poster_path')
                            ->label('Poster Event')
                            ->image()
                            ->directory('event-posters'),
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi & Syarat Ketentuan')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('poster_path')
                    ->label('Poster'),
                Tables\Columns\TextColumn::make('nama_event')
                    ->label('Nama Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pelaksanaan')
                    ->label('Pelaksanaan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('batas_pendaftaran')
                    ->label('Batas Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuota_tenant')
                    ->label('Kuota')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Total Pendaftar')
                    ->counts('applications')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('biaya_booth')
                    ->label('Biaya Booth')
                    ->money('IDR', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pendaftaran_dibuka' => 'success',
                        'pendaftaran_ditutup' => 'danger',
                        'selesai' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'pendaftaran_dibuka' => 'Pendaftaran Dibuka',
                        'pendaftaran_ditutup' => 'Pendaftaran Ditutup',
                        'selesai' => 'Selesai',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pendaftaran_dibuka' => 'Pendaftaran Dibuka',
                        'pendaftaran_ditutup' => 'Pendaftaran Ditutup',
                        'selesai' => 'Selesai',
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
