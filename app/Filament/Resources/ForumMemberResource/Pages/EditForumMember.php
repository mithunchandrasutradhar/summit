<?php

namespace App\Filament\Resources\ForumMemberResource\Pages;

use App\Filament\Resources\ForumMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForumMember extends EditRecord
{
    protected static string $resource = ForumMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
