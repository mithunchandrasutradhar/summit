<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AwardNominationReviewResource\Pages;
use App\Models\AwardNominationReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AwardNominationReviewResource extends Resource
{
    protected static ?string $model = AwardNominationReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Awards';

    protected static ?string $navigationLabel = 'My Reviews';

    protected static ?string $modelLabel = 'Review';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'awards_jury']);
    }

    public static function canCreate(): bool
    {
        // Assignments are created from the nomination's "Jury Assignments"
        // relation manager, not from this scoped list.
        return false;
    }

    /**
     * Jury members only ever see reviews assigned to them; admins see all
     * — this is the "scoped Filament resource, only assigned nominations
     * visible" requirement from the plan.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }

        return $query->where('reviewer_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('nomination_reference')
                    ->label('Reference')
                    ->content(fn (AwardNominationReview $record) => $record->nomination?->reference_no),
                Forms\Components\Placeholder::make('nominee_name')
                    ->label('Nominee')
                    ->content(fn (AwardNominationReview $record) => $record->nomination?->name() ?? '—'),
                Forms\Components\Placeholder::make('category')
                    ->content(fn (AwardNominationReview $record) => $record->nomination?->category?->name),
                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Select::make('decision')
                    ->options([
                        'no_decision' => 'No Decision Yet',
                        'shortlist' => 'Recommend Shortlisting',
                        'reject' => 'Recommend Rejecting',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('comments')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomination.reference_no')
                    ->label('Reference')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('nomination.category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('score'),
                Tables\Columns\TextColumn::make('decision')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAwardNominationReviews::route('/'),
            'edit' => Pages\EditAwardNominationReview::route('/{record}/edit'),
        ];
    }
}
