<?php

use App\Models\AwardCategory;
use App\Models\AwardNomination;
use App\Models\AwardNominationReview;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['super_admin', 'admin', 'awards_jury'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

it('renders the award categories list, create and edit pages for an admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $this->actingAs($admin);

    $category = AwardCategory::factory()->create();

    $this->get('/admin/award-categories')->assertOk();
    $this->get('/admin/award-categories/create')->assertOk();
    $this->get("/admin/award-categories/{$category->id}/edit")->assertOk();
});

it('renders the award nominations list and edit pages for an admin, with a jury assignments relation manager', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $this->actingAs($admin);

    $nomination = AwardNomination::factory()->create();

    $this->get('/admin/award-nominations')->assertOk();
    // Filament only shows a relation manager's title as a tab heading when there
    // are 2+ managers (or a combined content tab); with only one manager here it
    // renders inline, so assert on the manager's own visible table/action labels.
    $this->get("/admin/award-nominations/{$nomination->id}/edit")->assertOk()->assertSee('Assign Reviewer');
});

it('denies award nominations access to a jury member', function () {
    $jury = User::factory()->create();
    $jury->assignRole('awards_jury');
    $this->actingAs($jury);

    $this->get('/admin/award-nominations')->assertForbidden();
});

it('scopes the review resource so a jury member only sees their own assigned reviews', function () {
    $juryA = User::factory()->create();
    $juryA->assignRole('awards_jury');
    $juryB = User::factory()->create();
    $juryB->assignRole('awards_jury');

    $reviewForA = AwardNominationReview::factory()->create(['reviewer_id' => $juryA->id]);
    $reviewForB = AwardNominationReview::factory()->create(['reviewer_id' => $juryB->id]);

    $this->actingAs($juryA);

    $this->get('/admin/award-nomination-reviews')
        ->assertOk()
        ->assertSee($reviewForA->nomination->reference_no)
        ->assertDontSee($reviewForB->nomination->reference_no);

    $this->get("/admin/award-nomination-reviews/{$reviewForA->id}/edit")->assertOk();
});

it('lets an admin see every jury review regardless of reviewer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $jury = User::factory()->create();
    $jury->assignRole('awards_jury');

    $review = AwardNominationReview::factory()->create(['reviewer_id' => $jury->id]);

    $this->actingAs($admin)
        ->get('/admin/award-nomination-reviews')
        ->assertOk()
        ->assertSee($review->nomination->reference_no);
});
