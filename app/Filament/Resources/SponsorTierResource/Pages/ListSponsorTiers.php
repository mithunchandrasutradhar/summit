<?php

namespace App\Filament\Resources\SponsorTierResource\Pages;

use App\Filament\Resources\SponsorTierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSponsorTiers extends ListRecords
{
    protected static string $resource = SponsorTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
