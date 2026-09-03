<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Umkm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Proses Bisnis';

    protected static ?string $modelLabel = 'Pendaftaran Event';

    protected static ?string $pluralModelLabel = 'Pendaftaran Event';

    public static function canEdit($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()?->isTenant()) {
            $query->whereHas('umkm', function (Builder $q) {
                $q->where('user_id', Auth::id());
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pendaftaran Event')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->label('Pilih Event Bazar')
                            ->default(fn () => request()->query('event_id'))
                            ->relationship(
                                name: 'event',
                                titleAttribute: 'nama_event',
                                modifyQueryUsing: fn (Builder $query) => Auth::user()?->isTenant()
                                    ? $query->where('status', 'pendaftaran_dibuka')
                                    : $query
                            )
                            ->getOptionLabelFromRecordUsing(fn (Event $record) => "{$record->nama_event} - Rp " . number_format($record->biaya_booth, 0, ',', '.'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn (?Application $record) => $record !== null),

                        Forms\Components\Select::make('umkm_id')
                            ->label('Profil UMKM')
                            ->options(function () {
                                if (Auth::user()?->isTenant()) {
                                    return Umkm::where('user_id', Auth::id())->pluck('nama_usaha', 'id');
                                }
                                return Umkm::pluck('nama_usaha', 'id');
                            })
                            ->default(fn () => Auth::user()?->umkm?->id)
                            ->required()
                            ->searchable()
                            ->disabled(fn (?Application $record) => $record !== null),

                        Forms\Components\Textarea::make('konsep_booth')
                            ->label('Konsep Booth & Detail Jualan')
                            ->placeholder('Jelaskan produk yang dijual, dekorasi booth, dan daya listrik/kompor yang digunakan...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Hasil Kurasi Penyelenggara')
                    ->visible(fn (?Application $record) => Auth::user()?->isAdmin() || $record !== null)
                    ->schema([
                        Forms\Components\Select::make('status_kurasi')
                            ->label('Status Kurasi')
                            ->options([
                                'menunggu' => 'Menunggu Kurasi',
                                'sedang_ditinjau' => 'Sedang Ditinjau',
                                'diterima' => 'Diterima (Terbitkan Tagihan)',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->disabled(fn () => !Auth::user()?->isAdmin()),

                        Forms\Components\TextInput::make('nilai_kurasi')
                            ->label('Nilai Kurasi (1-100)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->disabled(fn () => !Auth::user()?->isAdmin()),

                        Forms\Components\Select::make('booth_id')
                            ->label('Alokasi Booth')
                            ->options(function (?Application $record) {
                                if (!$record) {
                                    return [];
                                }
                                return Booth::where('event_id', $record->event_id)
                                    ->where(function ($q) use ($record) {
                                        $q->where('status', 'tersedia')
                                          ->orWhere('id', $record->booth_id);
                                    })
                                    ->pluck('kode_booth', 'id');
                            })
                            ->placeholder('- Belum Ditentukan -')
                            ->disabled(fn () => !Auth::user()?->isAdmin()),

                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan Admin / Penyelenggara')
                            ->rows(2)
                            ->columnSpanFull()
                            ->disabled(fn () => !Auth::user()?->isAdmin()),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.nama_event')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('umkm.nama_usaha')
                    ->label('UMKM')
                    ->searchable()
                    ->icon('heroicon-m-building-storefront'),

                Tables\Columns\TextColumn::make('umkm.kategori_usaha')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('status_kurasi')
                    ->label('Status Kurasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'sedang_ditinjau' => 'info',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'sedang_ditinjau' => 'Sedang Ditinjau',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('nilai_kurasi')
                    ->label('Nilai')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('payment.status')
                    ->label('Status Bayar')
                    ->badge()
                    ->placeholder('Belum Diterima')
                    ->color(fn (?string $state): string => match ($state) {
                        'lunas' => 'success',
                        'menunggu_verifikasi' => 'warning',
                        'belum_bayar' => 'danger',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'lunas' => 'Lunas',
                        'menunggu_verifikasi' => 'Verifikasi',
                        'belum_bayar' => 'Belum Bayar',
                        'ditolak' => 'Ditolak',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('booth.kode_booth')
                    ->label('Booth Assigned')
                    ->badge()
                    ->color('primary')
                    ->placeholder('- Belum -'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'nama_event')
                    ->label('Filter Event'),
                Tables\Filters\SelectFilter::make('status_kurasi')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'sedang_ditinjau' => 'Sedang Ditinjau',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([
                // Action Kurasi (Admin Only)
                Tables\Actions\Action::make('kurasi')
                    ->label('Kurasi Tenant')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->color('warning')
                    ->visible(fn () => Auth::user()?->isAdmin())
                    ->form([
                        Forms\Components\Select::make('status_kurasi')
                            ->label('Keputusan Kurasi')
                            ->options([
                                'sedang_ditinjau' => 'Sedang Ditinjau',
                                'diterima' => 'Diterima (Terbitkan Tagihan)',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('nilai_kurasi')
                            ->label('Nilai Kurasi (1-100)')
                            ->numeric()
                            ->default(85)
                            ->minValue(1)
                            ->maxValue(100),
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan untuk Tenant')
                            ->placeholder('Berikan alasan/catatan kurasi...'),
                    ])
                    ->action(function (Application $record, array $data): void {
                        $record->update([
                            'status_kurasi' => $data['status_kurasi'],
                            'nilai_kurasi' => $data['nilai_kurasi'] ?? null,
                            'catatan_admin' => $data['catatan_admin'] ?? null,
                        ]);

                        // Automatically generate Payment invoice if Accepted
                        if ($data['status_kurasi'] === 'diterima') {
                            if (!$record->payment) {
                                Payment::create([
                                    'application_id' => $record->id,
                                    'nomor_tagihan' => 'INV/' . date('Ymd') . '/' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                                    'jumlah_tagihan' => $record->event->biaya_booth,
                                    'status' => 'belum_bayar',
                                ]);
                            }
                            Notification::make()
                                ->title('Tenant Diterima!')
                                ->body('Pendaftaran diterima dan tagihan booth otomatis diterbitkan.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Status Kurasi Diperbarui')
                                ->success()
                                ->send();
                        }
                    }),

                // Action Alokasi Booth (Admin Only, when payment is lunas)
                Tables\Actions\Action::make('assign_booth')
                    ->label('Tentukan Booth')
                    ->icon('heroicon-m-squares-plus')
                    ->color('success')
                    ->visible(fn (Application $record) => Auth::user()?->isAdmin() && $record->status_kurasi === 'diterima' && $record->payment?->status === 'lunas')
                    ->form(fn (Application $record) => [
                        Forms\Components\Select::make('booth_id')
                            ->label('Pilih Booth Yang Tersedia')
                            ->options(
                                Booth::where('event_id', $record->event_id)
                                    ->where('status', 'tersedia')
                                    ->get()
                                    ->mapWithKeys(fn ($b) => [$b->id => "{$b->kode_booth} - Zona {$b->zona} ({$b->ukuran})"])
                            )
                            ->required(),
                    ])
                    ->action(function (Application $record, array $data): void {
                        $booth = Booth::find($data['booth_id']);
                        if ($booth) {
                            // Release old booth if any
                            if ($record->booth_id) {
                                Booth::where('id', $record->booth_id)->update(['status' => 'tersedia']);
                            }

                            $record->update(['booth_id' => $booth->id]);
                            $booth->update(['status' => 'terisi']);

                            Notification::make()
                                ->title('Booth Berhasil Dialokasikan!')
                                ->body("Booth {$booth->kode_booth} telah diberikan kepada {$record->umkm->nama_usaha}.")
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
