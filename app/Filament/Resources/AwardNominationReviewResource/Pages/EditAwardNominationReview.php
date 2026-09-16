<?php

namespace App\Filament\Resources\AwardNominationReviewResource\Pages;

use App\Filament\Resources\AwardNominationReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwardNominationReview extends EditRecord
{
    protected static string $resource = AwardNominationReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
