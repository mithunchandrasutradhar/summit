<?php

namespace App\Filament\Widgets;

use App\Models\AwardCategory;
use Filament\Widgets\ChartWidget;

class AwardNominationsByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Award Nominations by Category';

    // See SummitStatsOverview — Filament widgets lazy-load by default.
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $categories = AwardCategory::withCount('nominations')->forCurrentEvent()->orderBy('order')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nominations',
                    'data' => $categories->pluck('nominations_count')->all(),
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $categories->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
