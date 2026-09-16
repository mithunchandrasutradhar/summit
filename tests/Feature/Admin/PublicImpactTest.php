<?php

use App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Resources\AwardCategoryResource\Pages\EditAwardCategory;
use App\Filament\Resources\AwardNominationResource\Pages\EditAwardNomination;
use App\Filament\Resources\CampusProgramResource\Pages\EditCampusProgram;
use App\Filament\Resources\DistrictResource\Pages\EditDistrict;
use App\Filament\Resources\DivisionResource\Pages\EditDivision;
use App\Filament\Resources\ExhibitorResource\Pages\EditExhibitor;
use App\Filament\Resources\FaqResource\Pages\EditFaq;
use App\Filament\Resources\HomepageSectionResource\Pages\EditHomepageSection;
use App\Filament\Resources\MediaCoverageResource\Pages\EditMediaCoverage;
use App\Filament\Resources\NewsPostResource\Pages\EditNewsPost;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PartnerResource\Pages\EditPartner;
use App\Filament\Resources\SessionResource\Pages\EditSession;
use App\Filament\Resources\SpeakerResource\Pages\EditSpeaker;
use App\Filament\Resources\SponsorResource\Pages\EditSponsor;
use App\Filament\Resources\SponsorTierResource\Pages\EditSponsorTier;
use App\Filament\Resources\SuccessStoryResource\Pages\EditSuccessStory;
use App\Models\Announcement;
use App\Models\AwardCategory;
use App\Models\AwardNomination;
use App\Models\CampusProgram;
use App\Models\District;
use App\Models\Division;
use App\Models\Event;
use App\Models\Exhibitor;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\MediaCoverage;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Session;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\SponsorTier;
use App\Models\SuccessStory;
use App\Models\User;
use App\Settings\GeneralSettings;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

/**
 * For every CRUD feature with a public-facing representation: edit a field
 * through the real dashboard form, then hit the actual public page and
 * confirm the new value appears (and the old one doesn't). This is a
 * traceability sweep, not a UI test — it exists to catch the specific
 * failure mode of "the field saves, but nothing on the site actually reads
 * it," which grep-auditing this session already found several real
 * instances of (see the GeneralSettings/HomepageSection fixes alongside
 * this file).
 */
beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);

    $this->event = Event::factory()->create(['is_current' => true]);
});

it('reflects a division name change on its public index and show pages', function () {
    $division = Division::factory()->create(['name' => ['en' => 'Old Division Name', 'bn' => 'Old Division Name']]);

    Livewire::test(EditDivision::class, ['record' => $division->getKey()])
        ->fillForm(['name' => 'New Division Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/divisional-summits')->assertSee('New Division Name')->assertDontSee('Old Division Name');
    $this->get('/en/divisional-summits/'.$division->fresh()->slug)->assertSee('New Division Name');
});

it('reflects a district name change on its public show page', function () {
    $division = Division::factory()->create();
    $district = District::factory()->create(['division_id' => $division->id, 'event_id' => $this->event->id, 'name' => ['en' => 'Old District', 'bn' => 'Old District']]);

    Livewire::test(EditDistrict::class, ['record' => $district->getKey()])
        ->fillForm(['name' => 'New District', 'division_id' => $division->id, 'event_id' => $this->event->id, 'status' => 'upcoming'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/district-roadshows/'.$district->fresh()->slug)->assertSee('New District')->assertDontSee('Old District');
});

it('reflects a campus program name change on its public show page', function () {
    $program = CampusProgram::factory()->create(['event_id' => $this->event->id, 'institution_name' => ['en' => 'Old University', 'bn' => 'Old University']]);

    Livewire::test(EditCampusProgram::class, ['record' => $program->getKey()])
        ->fillForm(['institution_name' => 'New University', 'event_id' => $this->event->id, 'type' => 'university', 'status' => 'upcoming'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/campus-programs/'.$program->fresh()->slug)->assertSee('New University')->assertDontSee('Old University');
});

it('reflects a speaker name/designation change on the public speakers pages', function () {
    $speaker = Speaker::factory()->create(['name' => 'Old Name', 'designation' => 'Old Title', 'is_published' => true, 'is_featured' => false]);

    Livewire::test(EditSpeaker::class, ['record' => $speaker->getKey()])
        ->fillForm(['name' => 'New Name', 'designation' => 'New Title'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/speakers')->assertSee('New Name')->assertDontSee('Old Name');
    $this->get('/en/speaker/'.$speaker->fresh()->slug)->assertSee('New Name')->assertSee('New Title');
});

it('reflects a session title change on the public agenda pages', function () {
    $session = Session::factory()->create(['event_id' => $this->event->id, 'title' => ['en' => 'Old Session Title', 'bn' => 'Old Session Title'], 'is_published' => true]);

    Livewire::test(EditSession::class, ['record' => $session->getKey()])
        ->fillForm(['title' => 'New Session Title', 'event_id' => $this->event->id, 'type' => 'seminar'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/agenda')->assertSee('New Session Title')->assertDontSee('Old Session Title');
    $this->get('/en/agenda/'.$session->fresh()->slug)->assertSee('New Session Title');
});

it('reflects an announcement message change on the grand summit hub page', function () {
    $announcement = Announcement::factory()->create([
        'event_id' => $this->event->id,
        'message' => 'Old announcement message',
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    Livewire::test(EditAnnouncement::class, ['record' => $announcement->getKey()])
        ->fillForm(['message' => 'New announcement message', 'severity' => 'info'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/grand-summit')->assertSee('New announcement message')->assertDontSee('Old announcement message');
});

it('reflects an award category name change on the public categories page', function () {
    $category = AwardCategory::factory()->create(['event_id' => $this->event->id, 'name' => ['en' => 'Old Category', 'bn' => 'Old Category'], 'is_active' => true]);

    Livewire::test(EditAwardCategory::class, ['record' => $category->getKey()])
        ->fillForm(['name' => 'New Category', 'event_id' => $this->event->id])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/awards/categories')->assertSee('New Category')->assertDontSee('Old Category');
});

it('publishes a nomination to the public shortlist/winners pages the moment its status changes', function () {
    $category = AwardCategory::factory()->create(['event_id' => $this->event->id]);
    $nomination = AwardNomination::factory()->create([
        'category_id' => $category->id,
        'status' => 'submitted',
        'field_values' => ['nominee_name' => 'Traceable Nominee'],
    ]);

    $this->get('/en/awards/winners')->assertDontSee('Traceable Nominee');

    Livewire::test(EditAwardNomination::class, ['record' => $nomination->getKey()])
        ->fillForm(['status' => 'winner'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/awards/winners')->assertSee('Traceable Nominee');
});

it('reflects a sponsor tier price change on the public sponsorship opportunity page', function () {
    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id, 'name' => ['en' => 'Test Tier', 'bn' => 'Test Tier'], 'price' => 1000, 'is_active' => true]);

    Livewire::test(EditSponsorTier::class, ['record' => $tier->getKey()])
        ->fillForm(['price' => 99999])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/sponsorship-opportunity')->assertSee('99,999');
});

it('reflects a sponsor name change on the public sponsors page, and unpublishing removes it', function () {
    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);
    $sponsor = Sponsor::factory()->create(['tier_id' => $tier->id, 'name' => 'Old Sponsor Co', 'is_published' => true]);

    Livewire::test(EditSponsor::class, ['record' => $sponsor->getKey()])
        ->fillForm(['name' => 'New Sponsor Co', 'tier_id' => $tier->id])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/sponsors')->assertSee('New Sponsor Co')->assertDontSee('Old Sponsor Co');

    Livewire::test(EditSponsor::class, ['record' => $sponsor->getKey()])
        ->fillForm(['is_published' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/sponsors')->assertDontSee('New Sponsor Co');
});

it('reflects an exhibitor company name change on the public exhibition page', function () {
    $exhibitor = Exhibitor::factory()->create(['company_name' => 'Old Exhibitor Co', 'is_published' => true]);

    Livewire::test(EditExhibitor::class, ['record' => $exhibitor->getKey()])
        ->fillForm(['company_name' => 'New Exhibitor Co'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/exhibition')->assertSee('New Exhibitor Co')->assertDontSee('Old Exhibitor Co');
});

it('reflects a news post title change on the public news pages', function () {
    $post = NewsPost::factory()->create(['title' => ['en' => 'Old News Title', 'bn' => 'Old News Title'], 'published_at' => now()->subDay()]);

    Livewire::test(EditNewsPost::class, ['record' => $post->getKey()])
        ->fillForm(['title' => 'New News Title', 'category' => 'news', 'body' => '<p>Body</p>'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/news')->assertSee('New News Title')->assertDontSee('Old News Title');
    $this->get('/en/news/'.$post->fresh()->slug)->assertSee('New News Title');
});

it('reflects a success story headline change on the public pages', function () {
    $story = SuccessStory::factory()->create(['freelancer_name' => 'Jane Freelancer', 'headline' => ['en' => 'Old Headline', 'bn' => 'Old Headline'], 'is_featured' => true, 'published_at' => now()->subDay()]);

    Livewire::test(EditSuccessStory::class, ['record' => $story->getKey()])
        ->fillForm(['headline' => 'New Headline'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/success-stories')->assertSee('New Headline')->assertDontSee('Old Headline');
    $this->get('/en/success-stories/'.$story->fresh()->slug)->assertSee('New Headline');
});

it('reflects a media coverage title change on the public media page', function () {
    $coverage = MediaCoverage::factory()->create(['title' => 'Old Coverage Title', 'published_at' => now()->subDay()]);

    Livewire::test(EditMediaCoverage::class, ['record' => $coverage->getKey()])
        ->fillForm(['title' => 'New Coverage Title'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/media')->assertSee('New Coverage Title')->assertDontSee('Old Coverage Title');
});

it('reflects a partner name change on the public partners page', function () {
    $partner = Partner::factory()->create(['name' => 'Old Partner Co', 'is_published' => true]);

    Livewire::test(EditPartner::class, ['record' => $partner->getKey()])
        ->fillForm(['name' => 'New Partner Co', 'category' => 'strategic'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/partners')->assertSee('New Partner Co')->assertDontSee('Old Partner Co');
});

it('reflects a static page body change on its public page', function () {
    $page = Page::factory()->create(['slug' => 'test-page', 'title' => ['en' => 'Test Page', 'bn' => 'Test Page'], 'body' => ['en' => '<p>Old body copy.</p>', 'bn' => '<p>Old body copy.</p>'], 'is_published' => true]);

    Livewire::test(EditPage::class, ['record' => $page->getKey()])
        ->fillForm(['body' => '<p>New body copy.</p>'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/test-page')->assertSee('New body copy.')->assertDontSee('Old body copy.');
});

it('reflects an FAQ question change on the public forum page', function () {
    $faq = Faq::factory()->create(['group' => 'forum', 'question' => ['en' => 'Old question?', 'bn' => 'Old question?'], 'is_active' => true]);

    Livewire::test(EditFaq::class, ['record' => $faq->getKey()])
        ->fillForm(['question' => 'New question?'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/forum')->assertSee('New question?')->assertDontSee('Old question?');
});

it('reflects the site name and contact details from General Settings on the public site', function () {
    Livewire::test(\App\Filament\Pages\ManageGeneralSettings::class)
        ->fillForm([
            'site_name' => 'Traceable Site Name',
            'contact_email' => 'traceable@example.com',
            'contact_phone' => '01700-999999',
        ])
        ->call('save');

    $settings = app(GeneralSettings::class);
    expect($settings->site_name)->toBe('Traceable Site Name');

    $response = $this->get('/en/');
    $response->assertSee('Traceable Site Name');
    $response->assertSee('traceable@example.com');
    $response->assertSee('01700-999999');
});

it('hides a homepage section from the public homepage when is_visible is turned off', function () {
    $section = HomepageSection::firstOrCreate(
        ['section_key' => 'success_stories'],
        ['is_visible' => true, 'order' => 90, 'content' => []]
    );

    \App\Models\SuccessStory::factory()->create(['is_featured' => true, 'headline' => ['en' => 'Visibility Toggle Story', 'bn' => 'x'], 'published_at' => now()->subDay()]);

    $this->get('/en/')->assertSee('Visibility Toggle Story');

    Livewire::test(EditHomepageSection::class, ['record' => $section->getKey()])
        ->fillForm(['is_visible' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/')->assertDontSee('Visibility Toggle Story');
});

it('reflects a homepage section heading override for a section that previously ignored it', function () {
    $section = HomepageSection::firstOrCreate(
        ['section_key' => 'latest_news'],
        ['is_visible' => true, 'order' => 110, 'content' => []]
    );

    NewsPost::factory()->create(['published_at' => now()->subDay()]);

    Livewire::test(EditHomepageSection::class, ['record' => $section->getKey()])
        ->fillForm(['content' => ['heading' => 'Custom News Heading From Admin']])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/en/')->assertSee('Custom News Heading From Admin');
});
