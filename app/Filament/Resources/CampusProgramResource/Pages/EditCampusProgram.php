<?php

namespace App\Filament\Resources\CampusProgramResource\Pages;

use App\Filament\Resources\CampusProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampusProgram extends EditRecord
{
    protected static string $resource = CampusProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
