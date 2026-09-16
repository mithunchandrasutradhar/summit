<?php

namespace App\Filament\Widgets;

use App\Models\AwardNomination;
use App\Models\ExhibitionBooth;
use App\Models\ExhibitorApplication;
use App\Models\ForumMember;
use App\Models\Registration;
use App\Models\Sponsor;
use App\Models\SponsorshipEnquiry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SummitStatsOverview extends BaseWidget
{
    // Filament widgets lazy-load by default (same CanBeLazy trait as
    // relation managers — see the Phase 7 fix on ReviewsRelationManager),
    // which means their real content never appears in a plain HTTP
    // response. This is the admin's primary reporting surface, so show it
    // eagerly rather than behind a scroll-triggered follow-up request.
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Registrations', Registration::count())
                ->description(Registration::whereNotNull('checked_in_at')->count().' checked in')
                ->descriptionIcon('heroicon-m-qr-code')
                ->icon('heroicon-o-identification')
                ->chart($this->trend(Registration::class))
                ->color('success'),

            Stat::make('Award Nominations', AwardNomination::count())
                ->description(AwardNomination::where('status', 'winner')->count().' winners selected')
                ->descriptionIcon('heroicon-m-trophy')
                ->icon('heroicon-o-star')
                ->chart($this->trend(AwardNomination::class))
                ->color('warning'),

            Stat::make('Sponsorship Pipeline', SponsorshipEnquiry::whereNotIn('status', ['closed'])->count())
                ->description(Sponsor::count().' confirmed sponsors')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->icon('heroicon-o-briefcase')
                ->chart($this->trend(SponsorshipEnquiry::class))
                ->color('info'),

            Stat::make('Exhibition Booths', ExhibitionBooth::where('status', 'confirmed')->count().' / '.ExhibitionBooth::count())
                ->description('confirmed of total')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->icon('heroicon-o-building-storefront')
                ->color('info'),

            Stat::make('Exhibitor Applications', ExhibitorApplication::count())
                ->description(ExhibitorApplication::where('status', 'confirmed')->count().' confirmed')
                ->icon('heroicon-o-clipboard-document-check')
                ->chart($this->trend(ExhibitorApplication::class)),

            Stat::make('Forum Members', ForumMember::where('status', 'approved')->count())
                ->description(ForumMember::where('status', 'submitted')->count().' pending review')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-user-group')
                ->chart($this->trend(ForumMember::class))
                ->color('success'),
        ];
    }

    /**
     * A simple 7-point trend of daily creation counts, for the widget's
     * mini sparkline — purely decorative, not a rigorous analytics feature.
     *
     * @return array<int, int>
     */
    protected function trend(string $model): array
    {
        $counts = $model::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        return collect(range(6, 0))
            ->map(fn (int $daysAgo) => (int) ($counts[now()->subDays($daysAgo)->toDateString()] ?? 0))
            ->all();
    }
}
