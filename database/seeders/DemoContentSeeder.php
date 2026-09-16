<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AwardCategory;
use App\Models\AwardNomination;
use App\Models\CampusProgram;
use App\Models\District;
use App\Models\Division;
use App\Models\Event;
use App\Models\ExhibitionBooth;
use App\Models\Exhibitor;
use App\Models\ExhibitorApplication;
use App\Models\ForumMember;
use App\Models\Gallery;
use App\Models\Hall;
use App\Models\MediaCoverage;
use App\Models\NewsPost;
use App\Models\Partner;
use App\Models\Session;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\SponsorshipEnquiry;
use App\Models\SponsorTier;
use App\Models\SuccessStory;
use App\Support\ReferenceNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Realistic-looking demo content across every public-facing feature, purely
 * so the redesigned UI has something real to render — not part of the
 * production content plan (see the other seeders for structural/placeholder
 * data). Safe to re-run: everything is looked up by a natural key first.
 */
class DemoContentSeeder extends Seeder
{
    protected array $tmpFiles = [];

    public function run(): void
    {
        $event = Event::current();

        if (! $event) {
            $this->command?->warn('No current event found — skipping demo content.');

            return;
        }

        $this->seedDivisionImages();
        $districts = $this->seedDistricts($event);
        $this->seedCampusPrograms($event, $districts);
        $halls = $this->seedHalls($event);
        $speakers = $this->seedSpeakers();
        $this->seedSessions($event, $halls, $speakers);
        $this->seedAnnouncements($event);
        $this->seedAwards($event);
        $this->seedSponsorship($event);
        $this->seedExhibition($event);
        $this->seedForum();
        $this->seedContent();
        $this->seedGalleries($event, $districts);

        $this->cleanupTmpFiles();

        $this->command?->info('Demo content seeded.');
    }

    /**
     * A simple solid-colour PNG with a text label — not a real photo, but
     * enough to exercise the whole responsive/WebP image pipeline and look
     * like *something* rather than an empty placeholder box.
     */
    protected function placeholderImage(string $label, string $hex, int $width = 800, int $height = 600): string
    {
        $image = imagecreatetruecolor($width, $height);
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');
        imagefill($image, 0, 0, imagecolorallocate($image, $r, $g, $b));

        $white = imagecolorallocate($image, 255, 255, 255);
        $font = 5;
        $lines = explode("\n", wordwrap($label, 22));
        $lineHeight = imagefontheight($font) + 8;
        $startY = ($height / 2) - (count($lines) * $lineHeight / 2);

        foreach ($lines as $i => $line) {
            $x = ($width - imagefontwidth($font) * strlen($line)) / 2;
            imagestring($image, $font, (int) $x, (int) ($startY + $i * $lineHeight), $line, $white);
        }

        $path = sys_get_temp_dir().'/seed-'.Str::random(12).'.png';
        imagepng($image, $path);

        $this->tmpFiles[] = $path;

        return $path;
    }

    protected function cleanupTmpFiles(): void
    {
        foreach ($this->tmpFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    protected function seedDivisionImages(): void
    {
        $colors = ['DD6103', '454F78', '94340C', '586494', 'B74306', '262B45', 'F98307', '181C30'];

        Division::orderBy('order')->get()->each(function (Division $division, int $i) use ($colors) {
            if ($division->getFirstMedia('image')) {
                return;
            }

            $division->addMedia($this->placeholderImage($division->name, $colors[$i % count($colors)], 1200, 800))
                ->toMediaCollection('image');
        });
    }

    /** @return \Illuminate\Support\Collection<int, District> */
    protected function seedDistricts(Event $event): \Illuminate\Support\Collection
    {
        $byDivision = [
            'Dhaka' => [['Dhaka', 'completed', 420], ['Gazipur', 'completed', 260]],
            'Chattogram' => [['Chattogram', 'completed', 310], ["Cox's Bazar", 'upcoming', 0]],
            'Rajshahi' => [['Rajshahi', 'completed', 180], ['Bogura', 'upcoming', 0]],
            'Khulna' => [['Khulna', 'completed', 200], ['Jessore', 'upcoming', 0]],
            'Barishal' => [['Barishal', 'upcoming', 0], ['Patuakhali', 'upcoming', 0]],
            'Sylhet' => [['Sylhet', 'completed', 150], ['Moulvibazar', 'upcoming', 0]],
            'Rangpur' => [['Rangpur', 'completed', 140], ['Dinajpur', 'upcoming', 0]],
            'Mymensingh' => [['Mymensingh', 'completed', 165], ['Jamalpur', 'upcoming', 0]],
        ];

        $created = collect();

        foreach (Division::all() as $division) {
            foreach ($byDivision[$division->getTranslation('name', 'en')] ?? [] as $order => [$name, $status, $participants]) {
                $district = District::firstOrCreate(
                    ['slug' => str($name)->slug(), 'event_id' => $event->id],
                    [
                        'division_id' => $division->id,
                        'name' => ['en' => $name, 'bn' => $name],
                        'description' => [
                            'en' => "A district roadshow bringing freelancing and digital economy awareness to {$name}, featuring local success stories, hands-on workshops and career guidance sessions.",
                            'bn' => "A district roadshow bringing freelancing and digital economy awareness to {$name}.",
                        ],
                        'venue' => "{$name} District Auditorium",
                        'event_date' => $status === 'completed'
                            ? now()->subMonths(random_int(1, 4))
                            : now()->addMonths(random_int(1, 3)),
                        'organizer_partner' => 'BACCO / District ICT Office',
                        'status' => $status,
                        'participants_count' => $participants,
                        'order' => $order,
                    ]
                );

                $created->push($district);
            }
        }

        return $created;
    }

    protected function seedCampusPrograms(Event $event, \Illuminate\Support\Collection $districts): void
    {
        $programs = [
            ['University of Dhaka', 'university', 'Dhaka'],
            ['BUET', 'university', 'Dhaka'],
            ['North South University', 'university', 'Dhaka'],
            ['BRAC University', 'university', 'Dhaka'],
            ['Dhaka Polytechnic Institute', 'polytechnic', 'Dhaka'],
            ['Chittagong University of Engineering & Technology', 'university', 'Chattogram'],
            ['University of Rajshahi', 'university', 'Rajshahi'],
            ['Khulna University', 'university', 'Khulna'],
            ['Shahjalal University of Science & Technology', 'university', 'Sylhet'],
            ['Rangpur Polytechnic Institute', 'polytechnic', 'Rangpur'],
            ['Jahangirnagar University', 'university', 'Dhaka'],
            ['Mymensingh Engineering College', 'university', 'Mymensingh'],
        ];

        $ambassadors = ['Tanjina Akter', 'Rakibul Hasan', 'Mehjabin Chowdhury', 'Shakil Ahmed', 'Nusrat Jahan Mim', 'Arif Hossain'];

        foreach ($programs as $order => [$name, $type, $districtName]) {
            $status = $order % 3 === 0 ? 'upcoming' : 'completed';
            $district = $districts->first(fn (District $d) => $d->getTranslation('name', 'en') === $districtName);

            CampusProgram::firstOrCreate(
                ['slug' => str($name)->slug(), 'event_id' => $event->id],
                [
                    'district_id' => $district?->id,
                    'institution_name' => ['en' => $name, 'bn' => $name],
                    'type' => $type,
                    'event_date' => $status === 'completed' ? now()->subMonths(random_int(1, 5)) : now()->addMonths(random_int(1, 3)),
                    'venue' => "{$name} Auditorium",
                    'description' => [
                        'en' => "A campus activation at {$name} introducing students to freelancing, the AI-powered digital economy and how to get started as an independent professional.",
                        'bn' => "A campus activation at {$name} introducing students to freelancing and the digital economy.",
                    ],
                    'organizer_partner' => 'BACCO Campus Network',
                    'ambassador_name' => $ambassadors[$order % count($ambassadors)],
                    'ambassador_contact' => '01700-'.random_int(100000, 999999),
                    'status' => $status,
                    'order' => $order,
                ]
            );
        }
    }

    /** @return \Illuminate\Support\Collection<int, Hall> */
    protected function seedHalls(Event $event): \Illuminate\Support\Collection
    {
        return collect([
            ['Bangabandhu Hall', 1200],
            ['Innovation Hall', 400],
            ['Workshop Room A', 120],
        ])->map(fn ($h) => Hall::firstOrCreate(
            ['event_id' => $event->id, 'name' => $h[0]],
            ['capacity' => $h[1]]
        ));
    }

    /** @return \Illuminate\Support\Collection<int, Speaker> */
    protected function seedSpeakers(): \Illuminate\Support\Collection
    {
        $speakers = [
            ['Farzana Chowdhury', 'Founder & CEO', 'TechNext BD', 'Bangladesh', true],
            ['Rafiul Islam', 'Country Manager', 'Upwork Bangladesh', 'Bangladesh', true],
            ['Nusrat Jahan', 'AI Research Lead', 'BRAC IT', 'Bangladesh', true],
            ['Tanvir Ahmed', 'Top Rated Freelancer', 'Toptal', 'Bangladesh', true],
            ['Sadia Islam Mou', 'Digital Marketing Strategist', 'Grameenphone', 'Bangladesh', false],
            ['Kamrul Hasan', 'Chief Technology Officer', 'Brain Station 23', 'Bangladesh', true],
            ['Farhana Haque', 'Program Director', 'BACCO', 'Bangladesh', false],
            ['Imran Kabir', 'Founder', 'Freelancer Bhaban', 'Bangladesh', true],
            ['Priya Saha', 'Senior UX Designer', 'Google', 'Singapore', false],
            ['Mahfuzur Rahman', 'Joint Secretary', 'ICT Division', 'Bangladesh', true],
        ];

        $colors = ['DD6103', '454F78', 'B74306', '586494'];

        return collect($speakers)->map(function ($s, $i) use ($colors) {
            [$name, $designation, $org, $country, $featured] = $s;

            $speaker = Speaker::firstOrCreate(
                ['slug' => str($name)->slug()],
                [
                    'name' => $name,
                    'designation' => $designation,
                    'organization' => $org,
                    'country' => $country,
                    'bio' => [
                        'en' => "{$name} is {$designation} at {$org}, working at the intersection of freelancing, technology and Bangladesh's digital economy.",
                        'bn' => "{$name} is {$designation} at {$org}.",
                    ],
                    'expertise' => ['Freelancing', 'Digital Economy', 'AI'],
                    'is_featured' => $featured,
                    'is_published' => true,
                    'order' => $i,
                ]
            );

            if (! $speaker->getFirstMedia('photo')) {
                $speaker->addMedia($this->placeholderImage($name, $colors[$i % count($colors)], 500, 500))
                    ->toMediaCollection('photo');
            }

            return $speaker;
        });
    }

    protected function seedSessions(Event $event, \Illuminate\Support\Collection $halls, \Illuminate\Support\Collection $speakers): void
    {
        $sessions = [
            ['Opening Keynote: The Future of Freelancing in Bangladesh', 'keynote', 0],
            ['AI Tools Every Freelancer Should Know', 'ai_awareness', 1],
            ['From Freelancer to Entrepreneur: Scaling Your Practice', 'freelancer_to_entrepreneur', 2],
            ['Winning on Global Marketplaces', 'marketplace', 0],
            ['Hands-on Workshop: Building Your Portfolio', 'workshop', 1],
            ['Panel: Policy Support for the Digital Workforce', 'panel', 0],
            ['Career Talk: Landing Your First International Client', 'career_talk', 2],
            ['Success Story Spotlight', 'success_story', 1],
            ['Freelancer Awards Ceremony', 'award', 0],
            ['Closing Networking Reception', 'networking', 0],
        ];

        foreach ($sessions as $order => [$title, $type, $hallIndex]) {
            $date = $event->date_start ? $event->date_start->copy()->addDays($order % 3) : now()->addMonths(2);

            $session = Session::firstOrCreate(
                ['slug' => str($title)->slug(), 'event_id' => $event->id],
                [
                    'title' => ['en' => $title, 'bn' => $title],
                    'description' => [
                        'en' => "Join us for \"{$title}\" — part of the Freelancer Summit Bangladesh 2026 agenda.",
                        'bn' => "Join us for \"{$title}\".",
                    ],
                    'type' => $type,
                    'track' => 'Main Track',
                    'hall_id' => $halls[$hallIndex % $halls->count()]->id,
                    'date' => $date,
                    'start_time' => sprintf('%02d:00', 9 + ($order % 6)),
                    'end_time' => sprintf('%02d:00', 10 + ($order % 6)),
                    'is_published' => true,
                    'order' => $order,
                ]
            );

            if ($session->speakers()->count() === 0) {
                $session->speakers()->attach($speakers[$order % $speakers->count()]->id, ['role' => 'speaker']);
                $session->speakers()->attach($speakers[($order + 1) % $speakers->count()]->id, ['role' => 'panelist']);
            }
        }
    }

    protected function seedAnnouncements(Event $event): void
    {
        $announcements = [
            ['Registration is now open!', 'Register today to secure your spot at Freelancer Summit Bangladesh 2026.', 'info'],
            ['Gate opens at 8:00 AM', 'Please arrive early on the day of the summit — gates open at 8:00 AM for check-in.', 'urgent'],
        ];

        foreach ($announcements as $a) {
            Announcement::firstOrCreate(
                ['event_id' => $event->id, 'title' => $a[0]],
                [
                    'message' => $a[1],
                    'severity' => $a[2],
                    'starts_at' => now()->subDay(),
                    'ends_at' => now()->addMonths(6),
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedAwards(Event $event): void
    {
        $categories = [
            ['Rising Freelancer of the Year', 'For freelancers in their first two years who have shown exceptional growth.'],
            ['Top Digital Marketplace Champion', 'For freelancers with outstanding ratings and earnings on global marketplaces.'],
            ['Women in Freelancing Excellence', 'Celebrating women leading the way in Bangladesh\'s freelance economy.'],
        ];

        foreach ($categories as $order => [$name, $description]) {
            AwardCategory::firstOrCreate(
                ['slug' => str($name)->slug(), 'event_id' => $event->id],
                [
                    'name' => ['en' => $name, 'bn' => $name],
                    'description' => ['en' => $description, 'bn' => $description],
                    'eligibility' => ['en' => 'Open to all Bangladeshi freelancers and digital professionals.', 'bn' => 'Open to all Bangladeshi freelancers.'],
                    'criteria' => ['en' => 'Judged on client feedback, earnings growth, and community contribution.', 'bn' => 'Judged on client feedback and growth.'],
                    'submission_deadline' => now()->addMonths(2),
                    'is_active' => true,
                    'order' => $order + 1,
                ]
            );
        }

        $categoryIds = AwardCategory::forCurrentEvent()->pluck('id');

        if ($categoryIds->isEmpty()) {
            return;
        }

        $nominees = [
            ['Mahmuda Akhter', 'submitted'],
            ['Rezaul Karim', 'under_review'],
            ['Shathi Rahman', 'shortlisted'],
            ['Golam Mostofa', 'winner'],
            ['Afsana Mimi', 'rejected'],
            ['Nayeem Uddin', 'incomplete'],
        ];

        foreach ($nominees as $i => [$name, $status]) {
            AwardNomination::firstOrCreate(
                ['nominee_email' => str($name)->slug().'@example.com'],
                [
                    'category_id' => $categoryIds[$i % $categoryIds->count()],
                    'reference_no' => ReferenceNumber::generate('award_nominations', 'AWD'),
                    'declaration_accepted_at' => now()->subDays(random_int(5, 30)),
                    'status' => $status,
                    'is_public_shortlisted' => in_array($status, ['shortlisted', 'winner'], true),
                    'is_public_winner' => $status === 'winner',
                    'decided_at' => in_array($status, ['shortlisted', 'winner', 'rejected'], true) ? now()->subDays(2) : null,
                    'field_values' => [
                        'nominee_name' => $name,
                        'nomination_reason' => "{$name} has demonstrated exceptional growth and client satisfaction as a freelancer over the past year.",
                    ],
                ]
            );
        }
    }

    protected function seedSponsorship(Event $event): void
    {
        // The 4 tiers already exist (SponsorshipExhibitionSeeder) as
        // zero-priced placeholders pending real pricing from BACCO — give
        // them demo pricing/benefits here so the sponsorship page looks real.
        $pricing = [
            'Title Sponsor' => ['price' => 2500000, 'benefits' => 'Premier branding across all summit materials, keynote speaking slot, largest exhibition booth.'],
            'Platinum Sponsor' => ['price' => 1200000, 'benefits' => 'Prime logo placement, panel speaking slot, premium exhibition booth.'],
            'Gold Sponsor' => ['price' => 600000, 'benefits' => 'Logo on event signage and website, standard exhibition booth.'],
            'Silver Sponsor' => ['price' => 250000, 'benefits' => 'Logo on website, shared exhibition space.'],
        ];

        foreach ($pricing as $name => $data) {
            $tier = SponsorTier::where('event_id', $event->id)->where('slug', str($name)->slug())->first();

            if ($tier && (float) $tier->price === 0.0) {
                $tier->update([
                    'price' => $data['price'],
                    'benefits' => ['en' => $data['benefits'], 'bn' => $data['benefits']],
                ]);
            }
        }

        $tiers = SponsorTier::where('event_id', $event->id)->get()->keyBy(fn ($t) => $t->getTranslation('name', 'en'));

        $sponsors = [
            ['Grameenphone', 'Title Sponsor'],
            ['bKash', 'Platinum Sponsor'],
            ['Banglalink', 'Platinum Sponsor'],
            ['Robi Axiata', 'Gold Sponsor'],
            ['Pathao', 'Gold Sponsor'],
            ['Brain Station 23', 'Silver Sponsor'],
            ['Selise', 'Silver Sponsor'],
            ['Optimizely', 'Silver Sponsor'],
        ];

        foreach ($sponsors as $order => [$name, $tierName]) {
            $tier = $tiers->get($tierName);

            if (! $tier) {
                continue;
            }

            $sponsor = Sponsor::firstOrCreate(
                ['name' => $name],
                [
                    'tier_id' => $tier->id,
                    'is_published' => true,
                    'order' => $order,
                ]
            );

            if (! $sponsor->getFirstMedia('logo')) {
                $sponsor->addMedia($this->placeholderImage($name, '454F78', 400, 200))->toMediaCollection('logo');
            }
        }

        $enquiries = [
            ['Zubair Enterprises', 'zubair@example.com', 'new'],
            ['DigitalReach Agency', 'contact@digitalreach.example.com', 'contacted'],
            ['NextGen Solutions', 'hello@nextgen.example.com', 'negotiation'],
        ];

        foreach ($enquiries as [$company, $email, $status]) {
            SponsorshipEnquiry::firstOrCreate(
                ['email' => $email],
                [
                    'reference_no' => ReferenceNumber::generate('sponsorship_enquiries', 'SPN'),
                    'sponsor_tier_id' => $tiers->first()?->id,
                    'status' => $status,
                    'field_values' => ['company_name' => $company, 'contact_person' => 'Contact Person', 'phone' => '01700-000000'],
                ]
            );
        }
    }

    protected function seedExhibition(Event $event): void
    {
        $booths = ExhibitionBooth::where('event_id', $event->id)->orderBy('booth_no')->get();

        $zones = ['Main Hall', 'Innovation Corner', 'Startup Alley'];

        $booths->each(function (ExhibitionBooth $booth, int $i) use ($zones) {
            if ((float) $booth->price === 0.0) {
                $booth->update([
                    'zone' => $zones[$i % count($zones)],
                    'price' => 50000 + ($i * 5000),
                ]);
            }
        });

        $exhibitorCompanies = ['Brain Station 23', 'Selise', 'Kaz Software', 'Enosis Solutions', 'Optimizely'];

        foreach ($exhibitorCompanies as $i => $name) {
            $booth = $booths[$i] ?? null;

            // Every exhibitor traces back to a (confirmed, paid) application —
            // exhibitors.application_id is a required FK, matching the real
            // application -> payment -> exhibitor lifecycle.
            $application = ExhibitorApplication::firstOrCreate(
                ['email' => str($name)->slug().'@example.com'],
                [
                    'reference_no' => ReferenceNumber::generate('exhibitor_applications', 'EXH'),
                    'preferred_booth_id' => $booth?->id,
                    'status' => 'confirmed',
                    'field_values' => ['organization_name' => $name, 'contact_person' => 'Contact Person', 'phone' => '01700-000000', 'sector' => 'Technology'],
                ]
            );

            $exhibitor = Exhibitor::firstOrCreate(
                ['company_name' => $name],
                [
                    'application_id' => $application->id,
                    'booth_id' => $booth?->id,
                    'sector' => 'Software & IT Services',
                    'description' => "{$name} is a leading Bangladeshi technology company showcasing its work at Freelancer Summit Bangladesh 2026.",
                    'is_published' => true,
                ]
            );

            if ($booth && $booth->status === 'available') {
                $booth->update(['status' => 'confirmed']);
            }

            if (! $exhibitor->getFirstMedia('logo')) {
                $exhibitor->addMedia($this->placeholderImage($name, 'B74306', 400, 200))->toMediaCollection('logo');
            }
        }

        $applications = [
            ['Anchorless Bangladesh', 'anchorless@example.com', 'new'],
            ['Vivasoft Limited', 'vivasoft@example.com', 'contacted'],
            ['Therap BD', 'therap@example.com', 'contacted'],
        ];

        $availableBooth = ExhibitionBooth::where('event_id', $event->id)->where('status', 'available')->first();

        foreach ($applications as [$company, $email, $status]) {
            ExhibitorApplication::firstOrCreate(
                ['email' => $email],
                [
                    'reference_no' => ReferenceNumber::generate('exhibitor_applications', 'EXH'),
                    'preferred_booth_id' => $availableBooth?->id,
                    'status' => $status,
                    'field_values' => ['organization_name' => $company, 'contact_person' => 'Contact Person', 'phone' => '01700-000000', 'sector' => 'Technology'],
                ]
            );
        }
    }

    protected function seedForum(): void
    {
        $members = [
            ['Farhan Sadiq', 'farhan.sadiq@example.com', 'approved', 'Web Development'],
            ['Ruma Begum', 'ruma.begum@example.com', 'approved', 'Graphic Design'],
            ['Shakib Al Rafi', 'shakib.rafi@example.com', 'submitted', 'Digital Marketing'],
            ['Tania Sultana', 'tania.sultana@example.com', 'submitted', 'Content Writing'],
            ['Hasibul Islam', 'hasibul.islam@example.com', 'rejected', 'Video Editing'],
        ];

        foreach ($members as [$name, $email, $status, $category]) {
            ForumMember::firstOrCreate(
                ['email' => $email],
                [
                    'reference_no' => ReferenceNumber::generate('forum_members', 'FRM'),
                    'consent_accepted_at' => now()->subDays(random_int(5, 60)),
                    'status' => $status,
                    'field_values' => ['full_name' => $name, 'freelancer_category' => $category],
                ]
            );
        }
    }

    protected function seedContent(): void
    {
        $news = [
            ['Registration Opens for Freelancer Summit Bangladesh 2026', 'news', true],
            ['8 Divisional Summits Announced Across Bangladesh', 'news', true],
            ['BACCO Unveils Freelancer Awards Categories for 2026', 'press_release', false],
            ['100 Campus Programs to Kick Off Nationwide', 'news', false],
            ['Grand Summit Agenda Released', 'news', true],
            ['Title Sponsor Grameenphone Joins Freelancer Summit 2026', 'press_release', false],
        ];

        foreach ($news as $i => [$title, $category, $featured]) {
            $post = NewsPost::firstOrCreate(
                ['slug' => str($title)->slug()],
                [
                    'title' => ['en' => $title, 'bn' => $title],
                    'category' => $category,
                    'excerpt' => ['en' => Str::limit($title, 100), 'bn' => Str::limit($title, 100)],
                    'body' => ['en' => "<p>{$title}. Full details will be shared as the summit approaches — stay tuned for updates from the organizing team.</p>", 'bn' => "<p>{$title}.</p>"],
                    'is_featured' => $featured,
                    'published_at' => now()->subDays($i * 3),
                ]
            );

            if (! $post->getFirstMedia('cover_image')) {
                $post->addMedia($this->placeholderImage($title, 'DD6103', 1200, 675))->toMediaCollection('cover_image');
            }
        }

        $stories = [
            ['Farzana Akter', 'Upwork', 'From Homemaker to Six-Figure Freelance Designer'],
            ['Mizanur Rahman', 'Fiverr', 'Building a Video Editing Empire from Rangpur'],
            ['Sabina Yasmin', 'Toptal', 'How I Became a Top-Rated Developer'],
            ['Kamruzzaman Rony', 'Freelancer.com', 'Scaling a One-Person Agency to a Team of 10'],
        ];

        foreach ($stories as $i => [$name, $marketplace, $headline]) {
            $story = SuccessStory::firstOrCreate(
                ['slug' => str($name)->slug()],
                [
                    'freelancer_name' => $name,
                    'marketplace' => $marketplace,
                    'headline' => ['en' => $headline, 'bn' => $headline],
                    'story' => ['en' => "{$name} started freelancing on {$marketplace} and has since built a thriving independent career, becoming an inspiration for aspiring freelancers across Bangladesh.", 'bn' => "{$name} started freelancing on {$marketplace}."],
                    'is_featured' => true,
                    'published_at' => now()->subDays($i * 5),
                ]
            );

            if (! $story->getFirstMedia('photo')) {
                $story->addMedia($this->placeholderImage($name, 'F98307', 600, 600))->toMediaCollection('photo');
            }
        }

        $coverage = [
            ['Freelancer Summit Bangladesh 2026 to Host Thousands', 'The Daily Star'],
            ['BACCO\'s National Push for the Digital Economy', 'Prothom Alo'],
            ['Bangladesh\'s Freelancers Get a National Stage', 'The Business Standard'],
            ['Inside the Country\'s Largest Freelancer Gathering', 'bdnews24.com'],
        ];

        foreach ($coverage as $i => [$title, $source]) {
            $item = MediaCoverage::firstOrCreate(
                ['title' => $title],
                [
                    'source_name' => $source,
                    'url' => 'https://example.com/coverage/'.str($title)->slug(),
                    'published_at' => now()->subDays($i * 7),
                ]
            );

            if (! $item->getFirstMedia('source_logo')) {
                $item->addMedia($this->placeholderImage($source, '262B45', 300, 150))->toMediaCollection('source_logo');
            }
        }

        $partners = [
            ['Bangladesh Association of Contact Center & Outsourcing', 'organizer'],
            ['ICT Division, Government of Bangladesh', 'government'],
            ['a2i Programme', 'government'],
            ['Grameenphone', 'strategic'],
            ['Google Bangladesh', 'technology'],
            ['Microsoft Bangladesh', 'technology'],
            ['BASIS', 'knowledge'],
            ['The Daily Star', 'media'],
        ];

        foreach ($partners as $order => [$name, $category]) {
            $partner = Partner::firstOrCreate(
                ['name' => $name],
                [
                    'category' => $category,
                    'order' => $order,
                    'is_published' => true,
                ]
            );

            if (! $partner->getFirstMedia('logo')) {
                $partner->addMedia($this->placeholderImage($name, '586494', 400, 200))->toMediaCollection('logo');
            }
        }
    }

    protected function seedGalleries(Event $event, \Illuminate\Support\Collection $districts): void
    {
        $eventGallery = Gallery::firstOrCreate(
            ['title' => 'Grand Summit Preview', 'related_type' => Event::class, 'related_id' => $event->id],
            ['description' => 'A preview gallery for the Grand Summit in Dhaka.']
        );

        if ($eventGallery->getMedia('photos')->isEmpty()) {
            foreach (['Main Stage', 'Exhibition Floor'] as $label) {
                $eventGallery->addMedia($this->placeholderImage($label, 'DD6103', 1000, 700))->toMediaCollection('photos');
            }
        }

        $districts->take(2)->each(function (District $district) {
            $gallery = Gallery::firstOrCreate(
                ['title' => $district->getTranslation('name', 'en').' Roadshow', 'related_type' => District::class, 'related_id' => $district->id],
                ['description' => 'Photos from the district roadshow.']
            );

            if ($gallery->getMedia('photos')->isEmpty()) {
                foreach (['Roadshow Crowd', 'Local Speakers'] as $label) {
                    $gallery->addMedia($this->placeholderImage($label, '454F78', 1000, 700))->toMediaCollection('photos');
                }
            }
        });
    }
}
