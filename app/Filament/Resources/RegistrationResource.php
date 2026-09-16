<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendBulkEmailAction;
use App\Filament\Exports\RegistrationExporter;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\FormField;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Registrations';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('reference_no')
                    ->content(fn (Registration $record) => $record->reference_no),
                Forms\Components\Placeholder::make('name')
                    ->content(fn (Registration $record) => $record->name() ?? '—'),
                Forms\Components\Placeholder::make('email')
                    ->content(fn (Registration $record) => $record->email),
                Forms\Components\Placeholder::make('mobile')
                    ->content(fn (Registration $record) => $record->mobile ?? '—'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(Registration::STATUSES, array_map('ucfirst', Registration::STATUSES)))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('name')
                    ->state(fn (Registration $record) => $record->name())
                    ->searchable(query: fn ($query, $search) => $query->whereJsonContains('field_values->name', $search)),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attendeeType.name')
                    ->label('Attendee Type'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\IconColumn::make('checked_in_at')
                    ->label('Checked In')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable(),
                ...static::filterableFieldColumns(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(Registration::STATUSES, array_map('ucfirst', Registration::STATUSES))),
                Tables\Filters\SelectFilter::make('attendee_type_id')
                    ->relationship('attendeeType', 'name')
                    ->label('Attendee Type'),
                ...static::filterableFieldFilters(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(RegistrationExporter::class),
                    SendBulkEmailAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }

    /**
     * Custom (non-system) fields an admin has marked is_filterable on the
     * summit_registration form — the mechanism plan §3a describes, minus
     * the generated-column indexing (an admin toggling this only affects a
     * handful of fields on a table this size, so a plain JSON path query is
     * sufficient rather than an ALTER TABLE run from inside a request).
     *
     * @return Collection<int, FormField>
     */
    protected static function filterableFields(): Collection
    {
        return FormField::query()
            ->whereHas('formDefinition', fn ($query) => $query->where('key', 'summit_registration'))
            ->where('is_filterable', true)
            ->where('is_system', false)
            ->get();
    }

    protected static function filterableFieldColumns(): array
    {
        return static::filterableFields()
            ->map(fn (FormField $field) => Tables\Columns\TextColumn::make('field_values.'.$field->field_key)
                ->label($field->label)
                ->toggleable())
            ->all();
    }

    protected static function filterableFieldFilters(): array
    {
        return static::filterableFields()
            ->map(fn (FormField $field) => Tables\Filters\Filter::make('field_'.$field->field_key)
                ->form([
                    Forms\Components\TextInput::make('value')->label($field->label),
                ])
                ->query(fn ($query, array $data) => $query->when(
                    $data['value'] ?? null,
                    fn ($query, $value) => $query->where('field_values->'.$field->field_key, 'like', '%'.$value.'%')
                ))
                ->indicateUsing(fn (array $data) => filled($data['value'] ?? null) ? [$field->label.': '.$data['value']] : []))
            ->all();
    }
}
