<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendBulkEmailAction;
use App\Filament\Exports\AwardNominationExporter;
use App\Filament\Resources\AwardNominationResource\Pages;
use App\Filament\Resources\AwardNominationResource\RelationManagers;
use App\Models\AwardNomination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AwardNominationResource extends Resource
{
    protected static ?string $model = AwardNomination::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Awards';

    protected static ?string $navigationLabel = 'Nominations';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('reference_no')
                    ->content(fn (AwardNomination $record) => $record->reference_no),
                Forms\Components\Placeholder::make('nominee_name')
                    ->content(fn (AwardNomination $record) => $record->name() ?? '—'),
                Forms\Components\Placeholder::make('nominee_email')
                    ->content(fn (AwardNomination $record) => $record->nominee_email),
                Forms\Components\Placeholder::make('category')
                    ->content(fn (AwardNomination $record) => $record->category?->name),
                Forms\Components\Select::make('status')
                    ->options(array_combine(
                        AwardNomination::STATUSES,
                        array_map(fn (string $s) => ucwords(str_replace('_', ' ', $s)), AwardNomination::STATUSES)
                    ))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nominee')
                    ->state(fn (AwardNomination $record) => $record->name()),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_public_shortlisted')
                    ->label('Shortlisted')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_public_winner')
                    ->label('Winner')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        AwardNomination::STATUSES,
                        array_map(fn (string $s) => ucwords(str_replace('_', ' ', $s)), AwardNomination::STATUSES)
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(AwardNominationExporter::class),
                    SendBulkEmailAction::make()
                        ->recipientEmail(fn (AwardNomination $record) => $record->nominee_email),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAwardNominations::route('/'),
            'edit' => Pages\EditAwardNomination::route('/{record}/edit'),
        ];
    }
}
