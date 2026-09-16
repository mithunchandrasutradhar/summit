<?php

namespace App\Filament\Resources\ExhibitorApplicationResource\Pages;

use App\Filament\Resources\ExhibitorApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExhibitorApplication extends EditRecord
{
    protected static string $resource = ExhibitorApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
