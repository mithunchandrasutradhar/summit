<?php

namespace App\Filament\Resources\AttendeeTypeResource\Pages;

use App\Filament\Resources\AttendeeTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendeeType extends EditRecord
{
    protected static string $resource = AttendeeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
