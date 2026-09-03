<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Booth;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Proses Bisnis';

    protected static ?string $modelLabel = 'Transaksi Pembayaran';

    protected static ?string $pluralModelLabel = 'Transaksi Pembayaran';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()?->isTenant()) {
            $query->whereHas('application.umkm', function (Builder $q) {
                $q->where('user_id', Auth::id());
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rincian Tagihan Booth')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_tagihan')
                            ->label('Nomor Tagihan')
                            ->disabled(),
                        Forms\Components\TextInput::make('jumlah_tagihan')
                            ->label('Jumlah Tagihan (Rp)')
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum_bayar' => 'Belum Bayar',
                                'menunggu_verifikasi' => 'Menunggu Verifikasi Admin',
                                'lunas' => 'Lunas (Diverifikasi)',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->disabled(fn () => !Auth::user()?->isAdmin()),
                        Forms\Components\DateTimePicker::make('tanggal_dibayar')
                            ->label('Waktu Waktu Upload / Pembayaran')
                            ->disabled(fn () => !Auth::user()?->isAdmin()),
                    ])->columns(2),

                Forms\Components\Section::make('Upload Bukti Transfer / QRIS')
                    ->schema([
                        Forms\Components\FileUpload::make('bukti_pembayaran_path')
                            ->label('File Bukti Transaksi')
                            ->image()
                            ->directory('payment-proofs')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan (Jika Ada)')
                            ->visible(fn ($record) => $record?->status === 'ditolak' || Auth::user()?->isAdmin())
                            ->disabled(fn () => !Auth::user()?->isAdmin())
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tagihan')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('application.event.nama_event')
                    ->label('Event')
                    ->searchable(),

                Tables\Columns\TextColumn::make('application.umkm.nama_usaha')
                    ->label('UMKM')
                    ->searchable()
                    ->icon('heroicon-m-building-storefront'),

                Tables\Columns\TextColumn::make('jumlah_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR', true)
                    ->sortable(),

                Tables\Columns\ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti Transfer')
                    ->square(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lunas' => 'success',
                        'menunggu_verifikasi' => 'warning',
                        'belum_bayar' => 'danger',
                        'ditolak' => 'rose',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lunas' => 'Lunas',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'belum_bayar' => 'Belum Bayar',
                        'ditolak' => 'Ditolak',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('tanggal_dibayar')
                    ->label('Tgl Bayar')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_bayar' => 'Belum Bayar',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'lunas' => 'Lunas',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([
                // Action Upload Bukti Pembayaran (Tenant)
                Tables\Actions\Action::make('upload_bukti')
                    ->label('Upload Bukti Transfer')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('warning')
                    ->visible(fn (Payment $record) => Auth::user()?->isTenant() && in_array($record->status, ['belum_bayar', 'ditolak']))
                    ->form([
                        Forms\Components\FileUpload::make('bukti_pembayaran_path')
                            ->label('Foto / Screenshot Bukti Transfer')
                            ->image()
                            ->directory('payment-proofs')
                            ->required(),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        $record->update([
                            'bukti_pembayaran_path' => $data['bukti_pembayaran_path'],
                            'status' => 'menunggu_verifikasi',
                            'tanggal_dibayar' => now(),
                        ]);

                        Notification::make()
                            ->title('Bukti Pembayaran Terkirim!')
                            ->body('Bukti transfer Anda telah terkirim dan menunggu verifikasi admin.')
                            ->success()
                            ->send();
                    }),

                // Action Verifikasi Pembayaran (Admin)
                Tables\Actions\Action::make('verifikasi')
                    ->label('Verifikasi Lunas')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Payment $record) => Auth::user()?->isAdmin() && $record->status === 'menunggu_verifikasi')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran Lunas')
                    ->modalDescription('Pastikan jumlah dana yang masuk sesuai dengan nominal tagihan.')
                    ->action(function (Payment $record): void {
                        $record->update([
                            'status' => 'lunas',
                            'tanggal_dibayar' => $record->tanggal_dibayar ?? now(),
                        ]);

                        Notification::make()
                            ->title('Pembayaran Diverifikasi Lunas!')
                            ->body('Status pembayaran telah menjadi Lunas. Silakan lakukan alokasi booth di menu Pendaftaran Event.')
                            ->success()
                            ->send();
                    }),

                // Action Tolak Pembayaran (Admin)
                Tables\Actions\Action::make('tolak_pembayaran')
                    ->label('Tolak Bukti')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Payment $record) => Auth::user()?->isAdmin() && $record->status === 'menunggu_verifikasi')
                    ->form([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Gambar bukti tidak terbaca / Nominal transfer kurang.')
                            ->required(),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        $record->update([
                            'status' => 'ditolak',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                        ]);

                        Notification::make()
                            ->title('Bukti Pembayaran Ditolak')
                            ->body('Tenant akan melihat catatan penolakan dan diminta mengunggah ulang.')
                            ->warning()
                            ->send();
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
