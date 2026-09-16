<?php

namespace App\Filament\Exports;

use App\Models\ExhibitorApplication;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExhibitorApplicationExporter extends Exporter
{
    protected static ?string $model = ExhibitorApplication::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference_no'),
            ExportColumn::make('organization')
                ->label('Organization')
                ->state(fn (ExhibitorApplication $record) => $record->organizationName()),
            ExportColumn::make('contact')
                ->label('Contact Person')
                ->state(fn (ExhibitorApplication $record) => $record->contactPerson()),
            ExportColumn::make('email'),
            ExportColumn::make('preferredBooth.booth_no')
                ->label('Booth'),
            ExportColumn::make('status'),
            ExportColumn::make('assignedTo.name')
                ->label('Assigned To'),
            ExportColumn::make('utm_source'),
            ExportColumn::make('utm_medium'),
            ExportColumn::make('utm_campaign'),
            ExportColumn::make('created_at')
                ->label('Submitted At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your exhibitor application export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
