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
                ->color('success'),

            Stat::make('Award Nominations', AwardNomination::count())
                ->description(AwardNomination::where('status', 'winner')->count().' winners selected')
                ->color('warning'),

            Stat::make('Sponsorship Pipeline', SponsorshipEnquiry::whereNotIn('status', ['closed'])->count())
                ->description(Sponsor::count().' confirmed sponsors')
                ->color('info'),

            Stat::make('Exhibition Booths', ExhibitionBooth::where('status', 'confirmed')->count().' / '.ExhibitionBooth::count())
                ->description('confirmed of total')
                ->color('info'),

            Stat::make('Exhibitor Applications', ExhibitorApplication::count())
                ->description(ExhibitorApplication::where('status', 'confirmed')->count().' confirmed'),

            Stat::make('Forum Members', ForumMember::where('status', 'approved')->count())
                ->description(ForumMember::where('status', 'submitted')->count().' pending review')
                ->color('success'),
        ];
    }
}
