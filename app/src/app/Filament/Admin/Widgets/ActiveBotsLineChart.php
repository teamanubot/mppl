<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DataPenyewaBot;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ActiveBotsLineChart extends ChartWidget
{
    protected static ?string $heading = '📈 Jumlah Bot Aktif per Jenis Bot';
    protected static ?string $description = 'Real-time statistik bot aktif: Selfbot & Official Bot';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $now = Carbon::now();

        $selfbotCount = DataPenyewaBot::where('jenisbot', 'selfbot')
            ->where('waktu_beli', '<=', $now)
            ->where('waktu_habis', '>=', $now)
            ->count();

        $officialBotCount = DataPenyewaBot::where('jenisbot', 'official bot')
            ->where('waktu_beli', '<=', $now)
            ->where('waktu_habis', '>=', $now)
            ->count();

        return [
            'labels' => ['Selfbot', 'Official Bot'],
            'datasets' => [
                [
                    'label' => 'Bot Aktif',
                    'data' => [$selfbotCount, $officialBotCount],
                    'fill' => true,
                    'backgroundColor' => 'rgba(96, 165, 250, 0.15)', // soft blue
                    'borderColor' => '#3b82f6', // blue-500
                    'pointBackgroundColor' => '#1d4ed8', // blue-700
                    'pointBorderColor' => '#fff',
                    'pointHoverRadius' => 6,
                    'pointRadius' => 5,
                    'pointStyle' => 'circle',
                    'tension' => 0.5,
                    'borderWidth' => 3,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}