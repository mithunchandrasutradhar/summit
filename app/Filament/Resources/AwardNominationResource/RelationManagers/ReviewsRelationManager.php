<?php

namespace App\Filament\Resources\AwardNominationResource\RelationManagers;

use App\Models\AwardNominationReview;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Jury Assignments';

    // AwardNominationReview has no Filament resource/policy of its own (it's only
    // ever accessed via this relation manager), so Filament's policy-based
    // authorization would deny it by default. Access is already gated at the
    // page level by AwardNominationResource::canAccess().
    protected static bool $shouldSkipAuthorization = true;

    // A nomination only ever has a handful of jury reviews, so there's no
    // performance reason to lazy-load this panel — and showing it eagerly
    // means jurors' assignments are visible immediately, without a loading flicker.
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reviewer_id')
                    ->label('Reviewer')
                    ->options(fn () => User::role('awards_jury')->pluck('name', 'id'))
                    ->required()
                    ->disabledOn('edit'),
                Forms\Components\Select::make('decision')
                    ->options(array_combine(
                        AwardNominationReview::DECISIONS,
                        array_map(fn (string $d) => ucwords(str_replace('_', ' ', $d)), AwardNominationReview::DECISIONS)
                    ))
                    ->default('no_decision'),
                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Textarea::make('comments')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewer'),
                Tables\Columns\TextColumn::make('score'),
                Tables\Columns\TextColumn::make('decision')
                    ->badge(),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(40),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Assign Reviewer'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
