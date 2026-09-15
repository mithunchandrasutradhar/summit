<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusProgramResource\Pages;
use App\Filament\Resources\CampusProgramResource\RelationManagers;
use App\Models\CampusProgram;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampusProgramResource extends Resource
{
    protected static ?string $model = CampusProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'Campus Programs';

    use Translatable;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('district_id')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->helperText('Optional — leave blank for a campus activation not tied to a specific district roadshow.'),
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->default(fn () => Event::where('is_current', true)->value('id')),
                Forms\Components\TextInput::make('institution_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->options(array_combine(CampusProgram::TYPES, array_map('ucfirst', CampusProgram::TYPES)))
                    ->required()
                    ->default('university'),
                Forms\Components\DatePicker::make('event_date'),
                Forms\Components\TextInput::make('venue'),
                Forms\Components\TextInput::make('organizer_partner')
                    ->label('Organizer / Partner'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ambassador_name')
                    ->label('Campus Ambassador'),
                Forms\Components\TextInput::make('ambassador_contact')
                    ->label('Ambassador Contact'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(CampusProgram::STATUSES, array_map('ucfirst', CampusProgram::STATUSES)))
                    ->required()
                    ->default('upcoming'),
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
                Tables\Columns\TextColumn::make('institution_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('District')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('ambassador_name')
                    ->label('Ambassador')
                    ->placeholder('—'),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(CampusProgram::TYPES, array_map('ucfirst', CampusProgram::TYPES))),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(CampusProgram::STATUSES, array_map('ucfirst', CampusProgram::STATUSES))),
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
            'index' => Pages\ListCampusPrograms::route('/'),
            'create' => Pages\CreateCampusProgram::route('/create'),
            'edit' => Pages\EditCampusProgram::route('/{record}/edit'),
        ];
    }
}
