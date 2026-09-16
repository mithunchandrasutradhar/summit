<?php

use App\Filament\Resources\AwardCategoryResource\Pages\CreateAwardCategory;
use App\Filament\Resources\AwardCategoryResource\Pages\EditAwardCategory;
use App\Filament\Resources\AwardNominationResource\Pages\EditAwardNomination;
use App\Filament\Resources\DistrictResource\Pages\CreateDistrict;
use App\Filament\Resources\DistrictResource\Pages\EditDistrict;
use App\Filament\Resources\ExhibitorApplicationResource\Pages\EditExhibitorApplication;
use App\Filament\Resources\ForumMemberResource\Pages\EditForumMember;
use App\Filament\Resources\NewsPostResource\Pages\CreateNewsPost;
use App\Filament\Resources\NewsPostResource\Pages\EditNewsPost;
use App\Filament\Resources\RegistrationResource\Pages\EditRegistration;
use App\Filament\Resources\SpeakerResource\Pages\CreateSpeaker;
use App\Filament\Resources\SpeakerResource\Pages\EditSpeaker;
use App\Filament\Resources\SponsorResource\Pages\CreateSponsor;
use App\Filament\Resources\SponsorResource\Pages\EditSponsor;
use App\Filament\Resources\SponsorshipEnquiryResource\Pages\EditSponsorshipEnquiry;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\AwardCategory;
use App\Models\AwardNomination;
use App\Models\District;
use App\Models\Division;
use App\Models\Event;
use App\Models\ExhibitionBooth;
use App\Models\ExhibitorApplication;
use App\Models\ForumMember;
use App\Models\NewsPost;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\SponsorshipEnquiry;
use App\Models\SponsorTier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

/**
 * Drives the real Filament dashboard Create/Edit form components — the same
 * classes, schemas, validation and save logic a Super Admin's browser talks
 * to — rather than creating rows directly, so these tests double as proof
 * the dashboard forms genuinely work end to end.
 */
beforeEach(function () {
    Storage::fake('public');

    foreach (['super_admin', 'admin', 'content_editor', 'sponsorship_manager', 'exhibition_manager', 'awards_jury'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');
    $this->actingAs($this->admin);

    $this->event = Event::factory()->create(['is_current' => true]);
});

it('creates and then edits a user through the dashboard forms', function () {
    $role = Role::where('name', 'content_editor')->first();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Jane Staff',
            'email' => 'jane.staff@example.com',
            'password' => 'password123',
            'roles' => [$role->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::where('email', 'jane.staff@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('content_editor'))->toBeTrue();

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm(['name' => 'Jane Staff-Updated'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->name)->toBe('Jane Staff-Updated');
});

it('creates and then edits a speaker through the dashboard forms, including a photo upload', function () {
    Livewire::test(CreateSpeaker::class)
        ->fillForm([
            'name' => 'Test Speaker',
            'slug' => 'test-speaker',
            'designation' => 'CEO',
            'organization' => 'Acme Co',
            'photo' => UploadedFile::fake()->image('speaker.jpg'),
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $speaker = Speaker::where('slug', 'test-speaker')->first();
    expect($speaker)->not->toBeNull();
    expect($speaker->getFirstMedia('photo'))->not->toBeNull();

    Livewire::test(EditSpeaker::class, ['record' => $speaker->getKey()])
        ->fillForm(['designation' => 'Founder & CEO'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($speaker->fresh()->designation)->toBe('Founder & CEO');
});

it('creates and then edits a district through the dashboard forms', function () {
    $division = Division::factory()->create();

    Livewire::test(CreateDistrict::class)
        ->fillForm([
            'division_id' => $division->id,
            'event_id' => $this->event->id,
            'name' => 'Test District',
            'slug' => 'test-district',
            'status' => 'upcoming',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $district = District::where('slug', 'test-district')->first();
    expect($district)->not->toBeNull();

    Livewire::test(EditDistrict::class, ['record' => $district->getKey()])
        ->fillForm(['status' => 'completed', 'participants_count' => 250])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($district->fresh()->status)->toBe('completed');
    expect($district->fresh()->participants_count)->toBe(250);
});

it('creates and then edits a news post through the dashboard forms, including a cover image upload', function () {
    Livewire::test(CreateNewsPost::class)
        ->fillForm([
            'title' => 'Test News Post',
            'slug' => 'test-news-post',
            'category' => 'news',
            'body' => '<p>Body content.</p>',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'published_at' => now(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = NewsPost::where('slug', 'test-news-post')->first();
    expect($post)->not->toBeNull();
    expect($post->getFirstMedia('cover_image'))->not->toBeNull();

    Livewire::test(EditNewsPost::class, ['record' => $post->getKey()])
        ->fillForm(['is_featured' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->fresh()->is_featured)->toBeTrue();
});

it('creates and then edits a sponsor through the dashboard forms, including a logo upload', function () {
    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);

    Livewire::test(CreateSponsor::class)
        ->fillForm([
            'name' => 'Test Sponsor Co',
            'tier_id' => $tier->id,
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $sponsor = Sponsor::where('name', 'Test Sponsor Co')->first();
    expect($sponsor)->not->toBeNull();
    expect($sponsor->getFirstMedia('logo'))->not->toBeNull();

    Livewire::test(EditSponsor::class, ['record' => $sponsor->getKey()])
        ->fillForm(['is_published' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($sponsor->fresh()->is_published)->toBeFalse();
});

it('creates and then edits an award category through the dashboard forms', function () {
    Livewire::test(CreateAwardCategory::class)
        ->fillForm([
            'event_id' => $this->event->id,
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $category = AwardCategory::where('slug', 'test-category')->first();
    expect($category)->not->toBeNull();

    Livewire::test(EditAwardCategory::class, ['record' => $category->getKey()])
        ->fillForm(['is_active' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($category->fresh()->is_active)->toBeFalse();
});

it('edits a forum member\'s status through the dashboard form (no create form by design)', function () {
    $member = ForumMember::factory()->create(['status' => 'submitted']);

    Livewire::test(EditForumMember::class, ['record' => $member->getKey()])
        ->fillForm(['status' => 'approved'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($member->fresh()->status)->toBe('approved');
});

it('edits a registration\'s status through the dashboard form (no create form by design)', function () {
    $registration = Registration::factory()->create(['status' => 'registered']);

    Livewire::test(EditRegistration::class, ['record' => $registration->getKey()])
        ->fillForm(['status' => 'cancelled'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($registration->fresh()->status)->toBe('cancelled');
});

it('edits a sponsorship enquiry\'s status and assignment through the dashboard form', function () {
    $tier = SponsorTier::factory()->create(['event_id' => $this->event->id]);
    $enquiry = SponsorshipEnquiry::factory()->create(['status' => 'new', 'sponsor_tier_id' => $tier->id]);

    Livewire::test(EditSponsorshipEnquiry::class, ['record' => $enquiry->getKey()])
        ->fillForm(['status' => 'contacted', 'assigned_to' => $this->admin->id])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($enquiry->fresh()->status)->toBe('contacted');
    expect($enquiry->fresh()->assigned_to)->toBe($this->admin->id);
});

it('assigns a booth to an exhibitor application through the dashboard form, and it reserves the booth', function () {
    $booth = ExhibitionBooth::factory()->create(['event_id' => $this->event->id, 'status' => 'available']);
    $application = ExhibitorApplication::factory()->create(['status' => 'new', 'preferred_booth_id' => null]);

    Livewire::test(EditExhibitorApplication::class, ['record' => $application->getKey()])
        ->fillForm(['preferred_booth_id' => $booth->id, 'status' => 'contacted'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($application->fresh()->preferred_booth_id)->toBe($booth->id);
    expect($booth->fresh()->status)->toBe('reserved');
});

it('edits an award nomination\'s status through the dashboard form', function () {
    $category = AwardCategory::factory()->create(['event_id' => $this->event->id]);
    $nomination = AwardNomination::factory()->create(['category_id' => $category->id, 'status' => 'submitted']);

    Livewire::test(EditAwardNomination::class, ['record' => $nomination->getKey()])
        ->fillForm(['status' => 'shortlisted'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($nomination->fresh()->status)->toBe('shortlisted');
    expect($nomination->fresh()->is_public_shortlisted)->toBeTrue();
});
