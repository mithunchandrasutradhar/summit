<?php

namespace App\Filament\Exports;

use App\Models\ActivationLead;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ActivationLeadExporter extends Exporter
{
    protected static ?string $model = ActivationLead::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('activatable_type'),
            ExportColumn::make('activatable_id'),
            ExportColumn::make('type'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('message'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your activation lead export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
