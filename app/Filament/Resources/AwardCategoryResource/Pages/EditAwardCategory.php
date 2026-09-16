<?php

namespace App\Filament\Resources\AwardCategoryResource\Pages;

use App\Filament\Resources\AwardCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwardCategory extends EditRecord
{
    protected static string $resource = AwardCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
