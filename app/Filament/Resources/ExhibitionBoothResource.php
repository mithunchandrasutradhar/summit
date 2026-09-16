<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExhibitionBoothResource\Pages;
use App\Models\Event;
use App\Models\ExhibitionBooth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExhibitionBoothResource extends Resource
{
    protected static ?string $model = ExhibitionBooth::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Exhibition';

    protected static ?string $navigationLabel = 'Booths';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'exhibition_manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->default(fn () => Event::where('is_current', true)->value('id')),
                Forms\Components\TextInput::make('booth_no')
                    ->required(),
                Forms\Components\TextInput::make('zone'),
                Forms\Components\TextInput::make('size')
                    ->placeholder('e.g. 3x3'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('BDT')
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->options(array_combine(
                        ExhibitionBooth::STATUSES,
                        array_map('ucfirst', ExhibitionBooth::STATUSES)
                    ))
                    ->default('available')
                    ->required(),
                Forms\Components\TextInput::make('map_x')
                    ->numeric(),
                Forms\Components\TextInput::make('map_y')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booth_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Edition'),
                Tables\Columns\TextColumn::make('zone'),
                Tables\Columns\TextColumn::make('size'),
                Tables\Columns\TextColumn::make('price')
                    ->money('BDT'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->defaultSort('booth_no')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ExhibitionBooth::STATUSES,
                        array_map('ucfirst', ExhibitionBooth::STATUSES)
                    )),
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
            'index' => Pages\ListExhibitionBooths::route('/'),
            'create' => Pages\CreateExhibitionBooth::route('/create'),
            'edit' => Pages\EditExhibitionBooth::route('/{record}/edit'),
        ];
    }
}
