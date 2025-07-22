<?php

namespace App\Filament\Admin\Resources;

use App\Models\PaymentTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Resources\PaymentTransactionResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Manajemen Pembayaran';
    protected static ?string $navigationLabel = 'Transaksi Pembayaran';
    protected static ?string $pluralModelLabel = 'Transaksi Pembayaran';
    protected static ?string $modelLabel = 'Transaksi';

    // ================================
    // FORM
    // ================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(['default' => 2])->schema([
                Forms\Components\Select::make('akun_id')
                    ->label('Nama Akun')
                    ->relationship('akun', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('product_id')
                    ->label('Jenis Langganan')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('midtrans_order_id')
                    ->label('Order ID Midtrans')
                    ->maxLength(255),

                Forms\Components\TextInput::make('midtrans_transaction_id')
                    ->label('Transaction ID Midtrans')
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\TextInput::make('currency')
                    ->label('Mata Uang')
                    ->default('IDR')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->maxLength(255),

                Forms\Components\Select::make('transaction_status')
                    ->label('Status Transaksi')
                    ->options([
                        'pending' => 'Pending',
                        'approved'    => 'Approved',
                        'settlement' => 'Settlement',
                        'rejected'  => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
            ]),

            Forms\Components\Grid::make(['default' => 3])->schema([
                Forms\Components\DateTimePicker::make('transaction_time')
                    ->label('Waktu Transaksi')
                    ->seconds(false),

                Forms\Components\DateTimePicker::make('settlement_time')
                    ->label('Waktu Penyelesaian')
                    ->seconds(false),

                Forms\Components\DateTimePicker::make('expiry_time')
                    ->label('Waktu Kadaluwarsa')
                    ->seconds(false),
            ]),

            Forms\Components\Textarea::make('raw_response')
                ->label('Raw Response')
                ->autosize()
                ->rows(6)
                ->columnSpanFull()
                ->maxLength(65535),
        ]);
    }

    // ================================
    // TABLE
    // ================================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('akun.name')
                    ->label('Nama Akun')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('product.name')
                    ->label('Jenis Langganan')
                    ->color(fn ($state) => match (strtolower($state)) {
                        'selfbot'       => 'primary',
                        'official bot'  => 'success',
                        default         => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('midtrans_order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('midtrans_transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->searchable()
                    ->limit(15),

                Tables\Columns\TextColumn::make('transaction_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved'    => 'success',
                        'settlement' => 'info',
                        'rejected'  => 'danger',
                        'expired' => 'warning',
                        default   => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('transaction_time')
                    ->label('Waktu Transaksi')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('settlement_time')
                    ->label('Waktu Penyelesaian')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiry_time')
                    ->label('Waktu Expired')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambah filter di sini jika dibutuhkan
                Tables\Filters\SelectFilter::make('transaction_status')
                    ->label('Status Transaksi')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                    ]),
                    
                Tables\Filters\Filter::make('transaction_time')
                    ->label('Waktu Transaksi')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('transaction_time', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('transaction_time', '<=', $date));
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ])
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // ================================
    // RELATIONS
    // ================================
    public static function getRelations(): array
    {
        return [];
    }

    // ================================
    // ROUTES
    // ================================
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPaymentTransactions::route('/'),
            'create' => Pages\CreatePaymentTransaction::route('/create'),
            'edit'   => Pages\EditPaymentTransaction::route('/{record}/edit'),
        ];
    }
}
