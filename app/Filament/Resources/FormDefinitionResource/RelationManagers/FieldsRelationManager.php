<?php

namespace App\Filament\Resources\FormDefinitionResource\RelationManagers;

use App\Models\FormField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $title = 'Fields';

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('field_key')
                    ->required()
                    ->disabledOn('edit')
                    ->helperText('Internal key, e.g. "company_name". Not shown publicly.'),
                Forms\Components\TextInput::make('label')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(array_combine(FormField::TYPES, array_map(
                        fn (string $t) => ucwords(str_replace('_', ' ', $t)),
                        FormField::TYPES
                    )))
                    ->required()
                    ->live()
                    ->default('text'),
                Forms\Components\TextInput::make('placeholder'),
                Forms\Components\KeyValue::make('options')
                    ->keyLabel('Value')
                    ->valueLabel('Label')
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select', 'radio']))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('relation_source')
                    ->label('Relation model (FQCN)')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'relation_select')
                    ->helperText('Fully-qualified model class, e.g. App\\Models\\AttendeeType.'),
                Forms\Components\TextInput::make('help_text'),
                Forms\Components\Toggle::make('is_required'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\Toggle::make('is_system')
                    ->disabled()
                    ->helperText('Set by the system for brief-specified default fields — not editable here.'),
                Forms\Components\TextInput::make('maps_to_column')
                    ->disabled()
                    ->helperText('When set, this field\'s value is written to a real database column instead of the custom fields store.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('field_key')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#'),
                Tables\Columns\TextColumn::make('label'),
                Tables\Columns\TextColumn::make('field_key')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_required')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_system')
                    ->label('Protected')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['order'] = $this->getOwnerRecord()->fields()->max('order') + 1;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (FormField $record) => ! $record->is_system),
            ]);
    }
}
