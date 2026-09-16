<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaCoverageResource\Pages;
use App\Models\MediaCoverage;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class MediaCoverageResource extends Resource
{
    protected static ?string $model = MediaCoverage::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Content';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'content_editor']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('source_name')
                    ->required(),
                Forms\Components\TextInput::make('url')
                    ->url()
                    ->required(),
                Forms\Components\DatePicker::make('published_at'),
                SpatieMediaLibraryFileUpload::make('source_logo')
                    ->collection('source_logo')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('source_logo')
                    ->collection('source_logo'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListMediaCoverages::route('/'),
            'create' => Pages\CreateMediaCoverage::route('/create'),
            'edit' => Pages\EditMediaCoverage::route('/{record}/edit'),
        ];
    }
}
