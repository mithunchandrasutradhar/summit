<?php

namespace App\Filament\Resources\SponsorTierResource\Pages;

use App\Filament\Resources\SponsorTierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsorTier extends EditRecord
{
    protected static string $resource = SponsorTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
