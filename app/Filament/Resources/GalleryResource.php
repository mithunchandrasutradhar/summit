<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Event;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\MorphToSelect::make('related')
                    ->types([
                        Forms\Components\MorphToSelect\Type::make(Event::class)
                            ->titleAttribute('name'),
                    ])
                    ->label('Attach to')
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('related_help')
                    ->label('')
                    ->content('More attachable types (Divisions, Districts, Campus Programs) become available as those modules are built.')
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('photos')
                    ->collection('photos')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('videos')
                    ->collection('videos')
                    ->multiple()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('related_type')
                    ->label('Attached To')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
