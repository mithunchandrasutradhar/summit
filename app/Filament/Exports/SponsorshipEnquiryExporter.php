<?php

namespace App\Filament\Exports;

use App\Models\SponsorshipEnquiry;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SponsorshipEnquiryExporter extends Exporter
{
    protected static ?string $model = SponsorshipEnquiry::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference_no'),
            ExportColumn::make('company')
                ->label('Company')
                ->state(fn (SponsorshipEnquiry $record) => $record->companyName()),
            ExportColumn::make('contact')
                ->label('Contact Person')
                ->state(fn (SponsorshipEnquiry $record) => $record->contactPerson()),
            ExportColumn::make('email'),
            ExportColumn::make('sponsorTier.name')
                ->label('Tier'),
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
        $body = 'Your sponsorship enquiry export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
