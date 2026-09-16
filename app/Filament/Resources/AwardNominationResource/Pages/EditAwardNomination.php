<?php

namespace App\Filament\Resources\AwardNominationResource\Pages;

use App\Filament\Resources\AwardNominationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwardNomination extends EditRecord
{
    protected static string $resource = AwardNominationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
