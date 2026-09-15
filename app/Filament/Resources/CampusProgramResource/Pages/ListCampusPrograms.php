<?php

namespace App\Filament\Resources\CampusProgramResource\Pages;

use App\Filament\Resources\CampusProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampusPrograms extends ListRecords
{
    protected static string $resource = CampusProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
