<?php

namespace App\Filament\Resources\ExhibitionBoothResource\Pages;

use App\Filament\Resources\ExhibitionBoothResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExhibitionBooth extends EditRecord
{
    protected static string $resource = ExhibitionBoothResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
