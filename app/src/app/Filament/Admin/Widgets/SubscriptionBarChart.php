<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;

class SubscriptionBarChart extends ChartWidget
{
    protected static ?string $heading = '📊 Jumlah Langganan per Jenis Langganan';

    protected static ?string $maxHeight = '360px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $products = Product::orderBy('name')->get();

        return [
            'labels' => $products->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Langganan Aktif',
                    'data' => $products->pluck('order')->toArray(),
                    'backgroundColor' => '#3B82F6', // blue-500
                    'borderRadius' => 8,
                    'barThickness' => 28,
                    'borderSkipped' => false,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'backgroundColor' => '#1f2937', // gray-800
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#d1d5db',
                    'cornerRadius' => 6,
                    'padding' => 10,
                    'titleFont' => [
                        'weight' => '600',
                        'size' => 14,
                    ],
                    'bodyFont' => [
                        'size' => 13,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'color' => '#6b7280', // gray-500
                        'font' => ['size' => 13],
                    ],
                    'grid' => [
                        'display' => false, // no vertical grid
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'color' => '#9ca3af', // gray-400
                        'font' => ['size' => 12],
                        'precision' => 0,
                    ],
                    'grid' => [
                        'color' => '#e5e7eb', // subtle light gray solid line
                        'borderDash' => [], // solid lines only
                    ],
                ],
            ],
        ];
    }
}