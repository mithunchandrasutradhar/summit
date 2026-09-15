<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public ?string $site_tagline;

    public ?string $countdown_target_at;

    public ?string $contact_email;

    public ?string $contact_phone;

    public ?string $contact_address;

    public ?string $social_facebook;

    public ?string $social_twitter;

    public ?string $social_linkedin;

    public ?string $social_youtube;

    public ?string $social_instagram;

    public ?string $ga_measurement_id;

    public ?string $gtm_container_id;

    public bool $sms_enabled;

    public ?string $default_og_image;

    public ?string $social_wall_embed_code;

    public static function group(): string
    {
        return 'general';
    }
}
