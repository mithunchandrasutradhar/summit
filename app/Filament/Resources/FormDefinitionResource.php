<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormDefinitionResource\Pages;
use App\Filament\Resources\FormDefinitionResource\RelationManagers;
use App\Models\FormDefinition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormDefinitionResource extends Resource
{
    protected static ?string $model = FormDefinition::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Form Builder';

    protected static ?string $navigationLabel = 'Registration Forms';

    protected static ?string $modelLabel = 'Form';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->helperText('Internal identifier used by the code, e.g. "summit_registration". Not shown publicly.'),
                Forms\Components\TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->badge(),
                Tables\Columns\TextColumn::make('fields_count')
                    ->counts('fields')
                    ->label('Fields'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormDefinitions::route('/'),
            'create' => Pages\CreateFormDefinition::route('/create'),
            'edit' => Pages\EditFormDefinition::route('/{record}/edit'),
        ];
    }
}
