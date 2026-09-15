<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Campaign';

    use Translatable;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('division_id')
                    ->relationship('division', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->default(fn () => \App\Models\Event::where('is_current', true)->value('id')),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('venue'),
                Forms\Components\DatePicker::make('event_date'),
                Forms\Components\TextInput::make('organizer_partner')
                    ->label('Organizer / Partner'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(District::STATUSES, array_map('ucfirst', District::STATUSES)))
                    ->required()
                    ->default('upcoming'),
                Forms\Components\TextInput::make('participants_count')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('speakers')
                    ->relationship('speakers', 'name')
                    ->multiple()
                    ->searchable()
                    ->columnSpanFull(),
                Forms\Components\Section::make('SEO')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('og_image')
                            ->image()
                            ->directory('seo'),
                        Forms\Components\Textarea::make('seo_description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('division.name')
                    ->label('Division')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Edition'),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Participants'),
                Tables\Columns\TextColumn::make('leads_count')
                    ->counts('activationLeads')
                    ->label('Leads'),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('division_id')
                    ->relationship('division', 'name')
                    ->label('Division'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(District::STATUSES, array_map('ucfirst', District::STATUSES))),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivationLeadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
