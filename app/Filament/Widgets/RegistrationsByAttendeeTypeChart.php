<?php

namespace App\Filament\Widgets;

use App\Models\AttendeeType;
use Filament\Widgets\ChartWidget;

class RegistrationsByAttendeeTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Registrations by Attendee Type';

    // See SummitStatsOverview — Filament widgets lazy-load by default.
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $types = AttendeeType::withCount('registrations')->orderBy('order')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Registrations',
                    'data' => $types->pluck('registrations_count')->all(),
                    'backgroundColor' => ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#06b6d4', '#a855f7', '#ec4899'],
                ],
            ],
            'labels' => $types->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
