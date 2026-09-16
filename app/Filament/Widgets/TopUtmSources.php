<?php

namespace App\Filament\Widgets;

use App\Models\AwardNomination;
use App\Models\ExhibitorApplication;
use App\Models\Registration;
use App\Models\SponsorshipEnquiry;
use Filament\Widgets\Widget;

class TopUtmSources extends Widget
{
    protected static string $view = 'filament.widgets.top-utm-sources';

    protected int|string|array $columnSpan = 'full';

    // See SummitStatsOverview — Filament widgets lazy-load by default.
    protected static bool $isLazy = false;

    /**
     * @return array<string, int>
     */
    public function getSources(): array
    {
        $counts = [];

        foreach ([Registration::class, AwardNomination::class, SponsorshipEnquiry::class, ExhibitorApplication::class] as $model) {
            $model::query()
                ->whereNotNull('utm_source')
                ->where('utm_source', '!=', '')
                ->selectRaw('utm_source, count(*) as total')
                ->groupBy('utm_source')
                ->pluck('total', 'utm_source')
                ->each(function ($total, $source) use (&$counts) {
                    $counts[$source] = ($counts[$source] ?? 0) + $total;
                });
        }

        arsort($counts);

        return array_slice($counts, 0, 10, true);
    }
}
