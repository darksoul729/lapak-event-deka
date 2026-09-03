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

    protected static function getQrisCardHtml(?Payment $record = null): string
    {
        $amount = $record ? number_format($record->jumlah_tagihan, 0, ',', '.') : '0';
        $invoiceNo = $record ? $record->nomor_tagihan : 'INV/TAGIHAN';

        return "
        <div class='max-w-sm mx-auto my-2 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-950 rounded-2xl border-2 border-gray-900 dark:border-gray-100 p-5 text-center shadow-xl'>
            <div class='flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-3 mb-3'>
                <span class='text-xs font-black tracking-widest text-red-600 uppercase bg-red-50 dark:bg-red-950/60 px-2 py-0.5 rounded border border-red-200 dark:border-red-900'>QRIS NATIONAL STANDARD</span>
                <span class='text-[10px] font-mono text-gray-500 dark:text-gray-400 font-semibold'>NMID: ID1026090388291</span>
            </div>

            <h3 class='text-base font-extrabold text-gray-900 dark:text-white uppercase tracking-tight'>LAPAK EVENT SAMARINDA</h3>
            <p class='text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5'>Bazar & Expo UMKM Kota Samarinda</p>

            <div class='my-4 p-3 bg-white rounded-xl border border-gray-300 dark:border-gray-700 inline-block shadow-inner'>
                <img src='https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=00020101021226680016COM.LAPAKEVENT.WWW01189360091100000000000215ID10260903882910303UMI51440014ID.CO.QRIS.WWW5204581253033605802ID5922LAPAKEVENT SAMARINDA6009SAMARINDA61057511162070703A0163047A8F' 
                     alt='QRIS Pembayaran' 
                     class='w-48 h-48 mx-auto object-contain'>
            </div>

            <div class='bg-emerald-50 dark:bg-emerald-950/60 rounded-xl p-3 border border-emerald-200 dark:border-emerald-800'>
                <p class='text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider'>Nominal Tagihan ({$invoiceNo})</p>
                <p class='text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-0.5'>
                    Rp {$amount}
                </p>
            </div>

            <div class='mt-3 text-[11px] text-gray-500 dark:text-gray-400 leading-snug font-medium'>
                Scan QRIS menggunakan <strong>GoPay, OVO, ShopeePay, DANA, BCA, Mandiri, BRI, BNI</strong> atau M-Banking lainnya.
            </div>
        </div>
        ";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('QRIS Stand Pembayaran')
                    ->schema([
                        Forms\Components\Placeholder::make('qris_display')
                            ->hiddenLabel()
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString(static::getQrisCardHtml($record))),
                    ]),

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
                            ->label('Waktu Upload / Pembayaran')
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
                    ->label('Bayar via QRIS')
                    ->icon('heroicon-m-qr-code')
                    ->color('warning')
                    ->visible(fn (Payment $record) => Auth::user()?->isTenant() && in_array($record->status, ['belum_bayar', 'ditolak']))
                    ->modalHeading('Pembayaran QRIS & Upload Bukti')
                    ->modalDescription('Silakan scan Kode QRIS resmi di bawah ini menggunakan GoPay/ShopeePay/M-Banking Anda, lalu lampirkan foto bukti transfer.')
                    ->form([
                        Forms\Components\Placeholder::make('qris_card')
                            ->hiddenLabel()
                            ->content(fn (Payment $record) => new \Illuminate\Support\HtmlString(static::getQrisCardHtml($record))),
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
