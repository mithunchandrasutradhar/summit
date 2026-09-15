<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuccessStoryResource\Pages;
use App\Models\SuccessStory;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class SuccessStoryResource extends Resource
{
    protected static ?string $model = SuccessStory::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Content';

    use Translatable;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('freelancer_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('marketplace')
                    ->maxLength(255),
                Forms\Components\TextInput::make('headline')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('story')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('video_url')
                    ->url()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->collection('photo')
                    ->image(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\Toggle::make('is_featured'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('freelancer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marketplace'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
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
            'index' => Pages\ListSuccessStories::route('/'),
            'create' => Pages\CreateSuccessStory::route('/create'),
            'edit' => Pages\EditSuccessStory::route('/{record}/edit'),
        ];
    }
}
