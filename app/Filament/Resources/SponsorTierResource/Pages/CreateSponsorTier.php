<?php

namespace App\Filament\Resources\SponsorTierResource\Pages;

use App\Filament\Resources\SponsorTierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSponsorTier extends CreateRecord
{
    protected static string $resource = SponsorTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
