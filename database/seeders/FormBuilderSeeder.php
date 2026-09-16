<?php

namespace Database\Seeders;

use App\Models\AttendeeType;
use App\Models\AwardCategory;
use App\Models\ExhibitionBooth;
use App\Models\FormDefinition;
use App\Models\FormField;
use App\Models\SponsorTier;
use Illuminate\Database\Seeder;

class FormBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedForm('summit_registration', 'Summit Registration', [
            ['name', 'Full Name', 'text', true],
            ['email', 'Email', 'email', true, 'email'],
            ['mobile', 'Mobile Number', 'tel', true, 'mobile'],
            ['attendee_type_id', 'I am a...', 'relation_select', true, 'attendee_type_id', null, null, AttendeeType::class],
            ['organization', 'Organization / Institution', 'text'],
            ['designation', 'Designation', 'text'],
            ['city', 'City', 'text'],
            ['interests', 'Interests', 'tags', false, null, null, 'Separate with commas, e.g. AI, Marketplaces, Freelancing'],
            ['consent', 'I agree to be contacted about the summit', 'checkbox', true, 'consent_accepted_at'],
        ]);

        $this->seedForm('award_nomination', 'Freelancer Award Nomination', [
            ['category_id', 'Award Category', 'relation_select', true, 'category_id', null, null, AwardCategory::class],
            ['nominee_name', 'Nominee / Applicant Name', 'text', true],
            ['nominee_email', 'Email', 'email', true, 'nominee_email'],
            ['nominee_phone', 'Phone', 'tel'],
            ['marketplace_profile', 'Marketplace Profile URL', 'url'],
            ['portfolio_links', 'Portfolio Links', 'textarea', false, null, null, 'One link per line'],
            ['achievements', 'Achievements', 'textarea'],
            ['supporting_documents', 'Supporting Document', 'file'],
            ['declaration', 'I declare the information provided is accurate', 'checkbox', true, 'declaration_accepted_at'],
        ]);

        $this->seedForm('sponsorship_enquiry', 'Sponsorship Enquiry', [
            ['sponsor_tier_id', 'Sponsorship Tier of Interest', 'relation_select', false, 'sponsor_tier_id', null, null, SponsorTier::class],
            ['company_name', 'Company Name', 'text', true],
            ['contact_person', 'Contact Person', 'text', true],
            ['designation', 'Designation', 'text'],
            ['phone', 'Phone', 'tel', true],
            ['email', 'Email', 'email', true, 'email'],
            ['sponsorship_interest', 'Sponsorship Interest', 'textarea'],
            ['budget_range', 'Budget Range', 'text'],
            ['requirements', 'Requirements', 'textarea'],
            ['message', 'Message', 'textarea'],
            ['callback_requested', 'Request a call back', 'checkbox', false, 'callback_requested_at'],
            ['preferred_contact_time', 'Preferred Contact Time', 'text', false, 'preferred_contact_time'],
        ]);

        $this->seedForm('exhibitor_application', 'Exhibition / Booth Registration', [
            ['preferred_booth_id', 'Preferred Booth', 'relation_select', false, 'preferred_booth_id', null, null, ExhibitionBooth::class],
            ['organization_name', 'Organization Name', 'text', true],
            ['sector', 'Sector', 'text'],
            ['booth_requirement', 'Booth Requirement', 'text'],
            ['contact_person', 'Contact Person', 'text', true],
            ['phone', 'Phone', 'tel', true],
            ['email', 'Email', 'email', true, 'email'],
            ['products_services', 'Products / Services', 'textarea'],
            ['branding_needs', 'Branding Needs', 'textarea'],
            ['requirements', 'Requirements', 'textarea'],
        ]);

        $this->seedForm('forum_membership', 'BACCO Freelancer Forum Registration', [
            ['full_name', 'Full Name', 'text', true],
            ['email', 'Email', 'email', true, 'email'],
            ['phone', 'Phone', 'tel'],
            ['freelancer_category', 'Category', 'select', false, null, [
                'freelancer' => 'Freelancer',
                'aspiring' => 'Aspiring Freelancer',
                'agency' => 'Agency / Company',
            ]],
            ['skills', 'Skills', 'tags'],
            ['marketplace_profile_links', 'Marketplace Profile Links', 'textarea', false, null, null, 'One link per line'],
            ['photo', 'Photo', 'file'],
            ['consent', 'I agree to the Forum membership terms', 'checkbox', true, 'consent_accepted_at'],
        ]);
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string, 3?: bool, 4?: string|null, 5?: array|null, 6?: string|null, 7?: string|null}>  $fields
     */
    private function seedForm(string $key, string $name, array $fields): void
    {
        $definition = FormDefinition::firstOrCreate(['key' => $key], ['name' => $name]);

        foreach ($fields as $order => $field) {
            [$fieldKey, $label, $type] = $field;
            $isRequired = $field[3] ?? false;
            $mapsToColumn = $field[4] ?? null;
            $options = $field[5] ?? null;
            $helpText = $field[6] ?? null;
            $relationSource = $field[7] ?? null;

            FormField::firstOrCreate(
                ['form_definition_id' => $definition->id, 'field_key' => $fieldKey],
                [
                    'label' => ['en' => $label, 'bn' => $label],
                    'type' => $type,
                    'options' => $options,
                    'relation_source' => $relationSource,
                    'is_required' => $isRequired,
                    'help_text' => $helpText,
                    'order' => $order,
                    'is_system' => true,
                    'maps_to_column' => $mapsToColumn,
                    'is_active' => true,
                ]
            );
        }
    }
}
