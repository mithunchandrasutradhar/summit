<?php

namespace App\Filament\Resources\CampaignStatResource\Pages;

use App\Filament\Resources\CampaignStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampaignStats extends ListRecords
{
    protected static string $resource = CampaignStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
