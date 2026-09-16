<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorTierResource\Pages;
use App\Models\Event;
use App\Models\SponsorTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorTierResource extends Resource
{
    protected static ?string $model = SponsorTier::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Sponsorship';

    protected static ?string $navigationLabel = 'Tiers';

    use Translatable;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'sponsorship_manager']);
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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('BDT')
                    ->default(0),
                Forms\Components\Textarea::make('benefits')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
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
                Tables\Columns\TextColumn::make('price')
                    ->money('BDT'),
                Tables\Columns\TextColumn::make('sponsors_count')
                    ->counts('sponsors')
                    ->label('Sponsors'),
                Tables\Columns\IconColumn::make('is_active')
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
            'index' => Pages\ListSponsorTiers::route('/'),
            'create' => Pages\CreateSponsorTier::route('/create'),
            'edit' => Pages\EditSponsorTier::route('/{record}/edit'),
        ];
    }
}
