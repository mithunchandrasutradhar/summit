<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignStatResource\Pages;
use App\Models\CampaignStat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampaignStatResource extends Resource
{
    protected static ?string $model = CampaignStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Campaign';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected static ?string $navigationLabel = 'National Journey Stats';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required(),
                Forms\Components\Select::make('key')
                    ->options(array_combine(
                        CampaignStat::KEYS,
                        array_map(fn (string $k) => ucwords(str_replace('_', ' ', $k)), CampaignStat::KEYS)
                    ))
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\Toggle::make('is_manual_override')
                    ->helperText('When on, the nightly recompute job leaves this value alone.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Edition'),
                Tables\Columns\TextColumn::make('key')
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('value')
                    ->numeric(),
                Tables\Columns\IconColumn::make('is_manual_override')
                    ->label('Manual')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaignStats::route('/'),
            'create' => Pages\CreateCampaignStat::route('/create'),
            'edit' => Pages\EditCampaignStat::route('/{record}/edit'),
        ];
    }
}
