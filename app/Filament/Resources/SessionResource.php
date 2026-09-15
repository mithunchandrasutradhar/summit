<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SessionResource\Pages;
use App\Models\Event;
use App\Models\Session;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Grand Summit';

    protected static ?string $navigationLabel = 'Agenda / Sessions';

    use Translatable;

    protected static function typeOptions(): array
    {
        return array_combine(
            Session::TYPES,
            array_map(fn (string $t) => ucwords(str_replace('_', ' ', $t)), Session::TYPES)
        );
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
                Forms\Components\Select::make('hall_id')
                    ->relationship('hall', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->options(self::typeOptions())
                    ->required()
                    ->default('seminar'),
                Forms\Components\TextInput::make('track'),
                Forms\Components\DatePicker::make('date'),
                Forms\Components\TimePicker::make('start_time'),
                Forms\Components\TimePicker::make('end_time'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('speakers')
                    ->relationship('speakers', 'name')
                    ->multiple()
                    ->searchable()
                    ->columnSpanFull()
                    ->helperText('To mark someone as moderator/panelist rather than speaker, adjust their role after saving from the Speakers relation.'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_published')
                    ->default(false),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('track'),
                Tables\Columns\TextColumn::make('hall.name')
                    ->label('Hall')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->time('h:i A'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(self::typeOptions()),
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->label('Hall'),
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
            'index' => Pages\ListSessions::route('/'),
            'create' => Pages\CreateSession::route('/create'),
            'edit' => Pages\EditSession::route('/{record}/edit'),
        ];
    }
}
