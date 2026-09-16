<?php

namespace App\Filament\Exports;

use App\Models\ForumMember;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ForumMemberExporter extends Exporter
{
    protected static ?string $model = ForumMember::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference_no'),
            ExportColumn::make('name')
                ->label('Name')
                ->state(fn (ForumMember $record) => $record->name()),
            ExportColumn::make('email'),
            ExportColumn::make('category')
                ->label('Category')
                ->state(fn (ForumMember $record) => $record->category()),
            ExportColumn::make('status'),
            ExportColumn::make('created_at')
                ->label('Submitted At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your forum member export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
