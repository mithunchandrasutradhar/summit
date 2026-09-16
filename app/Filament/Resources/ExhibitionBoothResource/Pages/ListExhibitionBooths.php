<?php

namespace App\Filament\Resources\ExhibitionBoothResource\Pages;

use App\Filament\Resources\ExhibitionBoothResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExhibitionBooths extends ListRecords
{
    protected static string $resource = ExhibitionBoothResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
