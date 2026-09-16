<?php

namespace App\Filament\Resources\SponsorshipEnquiryResource\Pages;

use App\Filament\Resources\SponsorshipEnquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsorshipEnquiry extends EditRecord
{
    protected static string $resource = SponsorshipEnquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
