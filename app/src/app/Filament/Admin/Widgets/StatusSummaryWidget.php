<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatusSummaryWidget extends BaseWidget
{
    protected ?string $heading = '📊 Ringkasan Status Pembayaran';

    protected function getCards(): array
    {
        $totalPendapatan = Status::where('payment_status', 'approved')->sum('price');
        $approvedCount = Status::where('payment_status', 'approved')->count();
        $pendingCount = Status::where('payment_status', 'pending')->count();
        $rejectedCount = Status::where('payment_status', 'rejected')->count();
        $totalLangganan = Status::count();

        return [
            Card::make('Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
                ->description('Total dari pembayaran yang disetujui')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Card::make('Status Disetujui', $approvedCount)
                ->description('Pembayaran dengan status approved')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Card::make('Status Menunggu', $pendingCount)
                ->description('Pembayaran masih diproses')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Card::make('Status Ditolak', $rejectedCount)
                ->description('Pembayaran gagal/tidak valid')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),

            Card::make('Total Langganan', $totalLangganan)
                ->description('Semua pelanggan aktif')
                ->color('primary')
                ->icon('heroicon-o-user-group'),
        ];
    }
}