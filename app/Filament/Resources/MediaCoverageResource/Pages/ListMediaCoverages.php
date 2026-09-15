<?php

namespace App\Filament\Resources\MediaCoverageResource\Pages;

use App\Filament\Resources\MediaCoverageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaCoverages extends ListRecords
{
    protected static string $resource = MediaCoverageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
