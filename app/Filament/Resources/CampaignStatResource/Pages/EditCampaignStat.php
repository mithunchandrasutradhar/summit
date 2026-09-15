<?php

namespace App\Filament\Resources\CampaignStatResource\Pages;

use App\Filament\Resources\CampaignStatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignStat extends EditRecord
{
    protected static string $resource = CampaignStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
