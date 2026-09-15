<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HallResource\Pages;
use App\Models\Event;
use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HallResource extends Resource
{
    protected static ?string $model = Hall::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Grand Summit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->default(fn () => Event::where('is_current', true)->value('id')),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('capacity')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Edition'),
                Tables\Columns\TextColumn::make('capacity'),
                Tables\Columns\TextColumn::make('sessions_count')
                    ->counts('sessions')
                    ->label('Sessions'),
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
            'index' => Pages\ListHalls::route('/'),
            'create' => Pages\CreateHall::route('/create'),
            'edit' => Pages\EditHall::route('/{record}/edit'),
        ];
    }
}
