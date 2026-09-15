<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakerResource\Pages;
use App\Models\Speaker;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class SpeakerResource extends Resource
{
    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';

    protected static ?string $navigationGroup = 'Grand Summit';

    use Translatable;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('designation'),
                Forms\Components\TextInput::make('organization'),
                Forms\Components\TextInput::make('country')
                    ->default('Bangladesh'),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->collection('photo')
                    ->image(),
                Forms\Components\Textarea::make('bio')
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('expertise')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('social_links')
                    ->keyLabel('Platform')
                    ->valueLabel('URL')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\Toggle::make('is_published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('designation'),
                Tables\Columns\TextColumn::make('organization'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeaker::route('/create'),
            'edit' => Pages\EditSpeaker::route('/{record}/edit'),
        ];
    }
}
