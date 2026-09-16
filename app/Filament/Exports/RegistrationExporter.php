<?php

namespace App\Filament\Exports;

use App\Models\FormField;
use App\Models\Registration;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RegistrationExporter extends Exporter
{
    protected static ?string $model = Registration::class;

    public static function getColumns(): array
    {
        // 'organization'/'city' stay as explicit columns for a stable, predictable
        // export shape; any other custom field an admin marks is_filterable is
        // appended automatically rather than requiring a code change per field.
        $staticFieldKeys = ['organization', 'city'];

        $dynamicColumns = FormField::query()
            ->whereHas('formDefinition', fn ($query) => $query->where('key', 'summit_registration'))
            ->where('is_filterable', true)
            ->where('is_system', false)
            ->whereNotIn('field_key', $staticFieldKeys)
            ->get()
            ->map(fn (FormField $field) => ExportColumn::make('field_values.'.$field->field_key)->label($field->label))
            ->all();

        return [
            ExportColumn::make('reference_no'),
            ExportColumn::make('name')
                ->state(fn (Registration $record) => $record->name()),
            ExportColumn::make('email'),
            ExportColumn::make('mobile'),
            ExportColumn::make('attendeeType.name')
                ->label('Attendee Type'),
            ExportColumn::make('field_values.organization')
                ->label('Organization'),
            ExportColumn::make('field_values.city')
                ->label('City'),
            ...$dynamicColumns,
            ExportColumn::make('status'),
            ExportColumn::make('checked_in_at'),
            ExportColumn::make('utm_source'),
            ExportColumn::make('utm_medium'),
            ExportColumn::make('utm_campaign'),
            ExportColumn::make('created_at')
                ->label('Registered At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your registration export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
