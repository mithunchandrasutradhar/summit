<?php

namespace App\Filament\Resources\SpeakerResource\Pages;

use App\Filament\Resources\SpeakerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSpeaker extends CreateRecord
{
    protected static string $resource = SpeakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
