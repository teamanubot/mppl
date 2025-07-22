<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LatestAccessLogs extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Aktivitas Terbaru';
    protected static ?int $sort = 100;
    protected int|string|array $columnSpan = 2;

    protected static function getLogNameColors(): array
    {
        $customs = [];

        foreach (config('filament-logger.custom') ?? [] as $custom) {
            if (filled($custom['color'] ?? null)) {
                $customs[$custom['color']] = $custom['log_name'];
            }
        }

        return array_merge(
            (config('filament-logger.resources.enabled') && config('filament-logger.resources.color')) ? [
                config('filament-logger.resources.color') => config('filament-logger.resources.log_name'),
            ] : [],
            (config('filament-logger.models.enabled') && config('filament-logger.models.color')) ? [
                config('filament-logger.models.color') => config('filament-logger.models.log_name'),
            ] : [],
            (config('filament-logger.access.enabled') && config('filament-logger.access.color')) ? [
                config('filament-logger.access.color') => config('filament-logger.access.log_name'),
            ] : [],
            (config('filament-logger.notifications.enabled') && config('filament-logger.notifications.color')) ? [
                config('filament-logger.notifications.color') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest()->take(5))
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->badge()
                    ->colors(self::getLogNameColors())
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->label(__('Tipe'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->label(__('Event'))
                    ->color('primary')
                    ->weight('medium')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Deskripsi'))
                    ->wrap()
                    ->limit(60)
                    ->tooltip(fn ($state) => $state),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('Entitas'))
                    ->formatStateUsing(function ($state, Model $record) {
                        if (! $state) return '-';

                        return Str::of($state)->afterLast('\\')->headline() . ' #' . $record->subject_id;
                    }),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('Pengguna'))
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Waktu'))
                    ->since() // tampilkan waktu relatif: "5 minutes ago"
                    ->sortable()
                    ->tooltip(fn ($record) => $record->created_at->format('d M Y, H:i')),
            ])
            ->striped() // tampilkan warna baris selang-seling agar lebih rapi
            ->paginated(false);
    }
}
