<?php

namespace App\Filament\Resources\NewsPostResource\Pages;

use App\Filament\Resources\NewsPostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsPost extends CreateRecord
{
    protected static string $resource = NewsPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
