<?php

namespace App\Filament\Exports;

use App\Models\AwardNomination;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AwardNominationExporter extends Exporter
{
    protected static ?string $model = AwardNomination::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference_no'),
            ExportColumn::make('name')
                ->label('Nominee Name')
                ->state(fn (AwardNomination $record) => $record->name()),
            ExportColumn::make('nominee_email'),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('status'),
            ExportColumn::make('is_public_shortlisted'),
            ExportColumn::make('is_public_winner'),
            ExportColumn::make('decided_at'),
            ExportColumn::make('utm_source'),
            ExportColumn::make('utm_medium'),
            ExportColumn::make('utm_campaign'),
            ExportColumn::make('created_at')
                ->label('Submitted At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your award nomination export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
