<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Freelancer Summit Bangladesh 2026');
        $this->migrator->add('general.site_tagline', null);
        $this->migrator->add('general.countdown_target_at', null);
        $this->migrator->add('general.contact_email', null);
        $this->migrator->add('general.contact_phone', null);
        $this->migrator->add('general.contact_address', null);
        $this->migrator->add('general.social_facebook', null);
        $this->migrator->add('general.social_twitter', null);
        $this->migrator->add('general.social_linkedin', null);
        $this->migrator->add('general.social_youtube', null);
        $this->migrator->add('general.social_instagram', null);
        $this->migrator->add('general.ga_measurement_id', null);
        $this->migrator->add('general.gtm_container_id', null);
        $this->migrator->add('general.sms_enabled', false);
        $this->migrator->add('general.default_og_image', null);
        $this->migrator->add('general.social_wall_embed_code', null);
    }
};
