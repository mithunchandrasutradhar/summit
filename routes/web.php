<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\Public\AgendaController;
use App\Http\Controllers\Public\AwardController;
use App\Http\Controllers\Public\CampusProgramController;
use App\Http\Controllers\Public\DistrictController;
use App\Http\Controllers\Public\DivisionController;
use App\Http\Controllers\Public\ExhibitionController;
use App\Http\Controllers\Public\ForumController;
use App\Http\Controllers\Public\GrandSummitController;
use App\Http\Controllers\Public\MediaController;
use App\Http\Controllers\Public\NationalJourneyController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PartnerController;
use App\Http\Controllers\Public\PaymentController;
use App\Http\Controllers\Public\RegistrationController;
use App\Http\Controllers\Public\SpeakerController;
use App\Http\Controllers\Public\SponsorshipController;
use App\Http\Controllers\Public\SuccessStoryController;
use App\Services\Seo\SitemapBuilder;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/checkin', [CheckInController::class, 'scanner'])->name('checkin.scanner');
    Route::get('/checkin/{token}', [CheckInController::class, 'checkIn'])->name('checkin.show');
});

// SSLCommerz callbacks — no locale prefix (the gateway has no notion of it)
// and no auth (server-to-server IPN, or a bare browser redirect back from
// the gateway with nothing but a tran_id).
Route::match(['get', 'post'], '/payments/sslcommerz/success', [PaymentController::class, 'success'])->name('payments.sslcommerz.success');
Route::match(['get', 'post'], '/payments/sslcommerz/fail', [PaymentController::class, 'fail'])->name('payments.sslcommerz.fail');
Route::match(['get', 'post'], '/payments/sslcommerz/cancel', [PaymentController::class, 'cancel'])->name('payments.sslcommerz.cancel');
Route::post('/payments/sslcommerz/ipn', [PaymentController::class, 'ipn'])->name('payments.sslcommerz.ipn');

Route::get('/sitemap.xml', fn (SitemapBuilder $builder) => $builder->build())->name('sitemap');

Route::redirect('/', '/'.config('app.locale').'/');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('app.supported_locales'))])
    ->middleware('locale')
    ->group(function () {
        Route::view('/', 'home')->name('home');

        Route::get('/news', [NewsController::class, 'index'])->name('news.index');
        Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

        Route::get('/success-stories', [SuccessStoryController::class, 'index'])->name('success-stories.index');
        Route::get('/success-stories/{slug}', [SuccessStoryController::class, 'show'])->name('success-stories.show');

        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');

        Route::get('/media', [MediaController::class, 'index'])->name('media.index');

        Route::get('/national-journey', [NationalJourneyController::class, 'index'])->name('national-journey.index');

        Route::get('/divisional-summits', [DivisionController::class, 'index'])->name('divisions.index');
        Route::get('/divisional-summits/{slug}', [DivisionController::class, 'show'])->name('divisions.show');

        Route::get('/district-roadshows', [DistrictController::class, 'index'])->name('districts.index');
        Route::get('/district-roadshows/{slug}', [DistrictController::class, 'show'])->name('districts.show');
        Route::post('/district-roadshows/{slug}/leads', [DistrictController::class, 'storeLead'])
            ->name('districts.leads.store')
            ->middleware('throttle:6,1');

        Route::get('/campus-programs', [CampusProgramController::class, 'index'])->name('campus-programs.index');
        Route::get('/campus-programs/{slug}', [CampusProgramController::class, 'show'])->name('campus-programs.show');
        Route::post('/campus-programs/{slug}/leads', [CampusProgramController::class, 'storeLead'])
            ->name('campus-programs.leads.store')
            ->middleware('throttle:6,1');

        Route::get('/grand-summit', [GrandSummitController::class, 'index'])->name('grand-summit.index');

        Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
        Route::get('/agenda/{slug}/calendar.ics', [AgendaController::class, 'ics'])->name('agenda.ics');
        Route::get('/agenda/{slug}', [AgendaController::class, 'show'])->name('agenda.show');

        Route::get('/speakers', [SpeakerController::class, 'index'])->name('speakers.index');
        Route::get('/speaker/{slug}', [SpeakerController::class, 'show'])->name('speakers.show');

        Route::get('/register', [RegistrationController::class, 'index'])->name('register.index');

        Route::get('/awards', [AwardController::class, 'index'])->name('awards.index');
        Route::get('/awards/categories', [AwardController::class, 'categories'])->name('awards.categories');
        Route::get('/awards/nominate', [AwardController::class, 'nominate'])->name('awards.nominate');
        Route::get('/awards/shortlisted', [AwardController::class, 'shortlisted'])->name('awards.shortlisted');
        Route::get('/awards/winners', [AwardController::class, 'winners'])->name('awards.winners');

        Route::get('/sponsors', [SponsorshipController::class, 'sponsors'])->name('sponsors.index');
        Route::get('/sponsorship-opportunity', [SponsorshipController::class, 'opportunity'])->name('sponsorship.opportunity');
        Route::get('/sponsorship-opportunity/pay/{enquiry}', [PaymentController::class, 'initiateSponsorship'])
            ->name('payments.sponsorship.initiate')
            ->middleware('signed');

        Route::get('/exhibition', [ExhibitionController::class, 'index'])->name('exhibition.index');
        Route::get('/exhibition/apply', [ExhibitionController::class, 'apply'])->name('exhibition.apply');
        Route::get('/exhibition/pay/{application}', [PaymentController::class, 'initiateExhibition'])
            ->name('payments.exhibition.initiate')
            ->middleware('signed');

        Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
        Route::get('/forum/register', [ForumController::class, 'register'])->name('forum.register');

        // Generic CMS page fallback — keep last so future phases can register
        // more specific top-level routes (awards, sponsorship, ...) above it.
        Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
    });
