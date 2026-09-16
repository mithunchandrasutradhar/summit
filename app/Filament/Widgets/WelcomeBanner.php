<?php

namespace App\Filament\Widgets;

use App\Models\AwardNomination;
use App\Models\ExhibitorApplication;
use App\Models\ForumMember;
use App\Models\Registration;
use App\Models\SponsorshipEnquiry;
use Filament\Widgets\Widget;

class WelcomeBanner extends Widget
{
    protected static string $view = 'filament.widgets.welcome-banner';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?int $sort = -10;

    public function getPendingReviewCount(): int
    {
        return AwardNomination::where('status', 'submitted')->count()
            + SponsorshipEnquiry::where('status', 'new')->count()
            + ExhibitorApplication::where('status', 'new')->count()
            + ForumMember::where('status', 'submitted')->count();
    }
}
