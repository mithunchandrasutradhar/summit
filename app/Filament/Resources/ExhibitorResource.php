<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExhibitorResource\Pages;
use App\Models\Exhibitor;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class ExhibitorResource extends Resource
{
    protected static ?string $model = Exhibitor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Exhibition';

    protected static ?string $navigationLabel = 'Exhibitors';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'exhibition_manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('company_name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('application_id')
                    ->relationship('application', 'reference_no')
                    ->label('Originating Application'),
                Forms\Components\Select::make('booth_id')
                    ->relationship('booth', 'booth_no'),
                Forms\Components\TextInput::make('website')
                    ->url(),
                Forms\Components\TextInput::make('sector'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logo')
                    ->image()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo'),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booth.booth_no')
                    ->label('Booth'),
                Tables\Columns\TextColumn::make('sector'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->defaultSort('company_name')
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
            'index' => Pages\ListExhibitors::route('/'),
            'create' => Pages\CreateExhibitor::route('/create'),
            'edit' => Pages\EditExhibitor::route('/{record}/edit'),
        ];
    }
}
