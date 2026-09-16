<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendBulkEmailAction;
use App\Filament\Exports\ForumMemberExporter;
use App\Filament\Resources\ForumMemberResource\Pages;
use App\Models\ForumMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ForumMemberResource extends Resource
{
    protected static ?string $model = ForumMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Forum';

    protected static ?string $navigationLabel = 'Members';

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
                    ->content(fn (ForumMember $record) => $record->reference_no),
                Forms\Components\Placeholder::make('full_name')
                    ->content(fn (ForumMember $record) => $record->name() ?? '—'),
                Forms\Components\Placeholder::make('email')
                    ->content(fn (ForumMember $record) => $record->email),
                Forms\Components\Placeholder::make('category')
                    ->content(fn (ForumMember $record) => $record->category() ?? '—'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(
                        ForumMember::STATUSES,
                        array_map('ucfirst', ForumMember::STATUSES)
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
                    ->label('Name')
                    ->state(fn (ForumMember $record) => $record->name()),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->state(fn (ForumMember $record) => $record->category()),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ForumMember::STATUSES,
                        array_map('ucfirst', ForumMember::STATUSES)
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(ForumMemberExporter::class),
                    SendBulkEmailAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForumMembers::route('/'),
            'edit' => Pages\EditForumMember::route('/{record}/edit'),
        ];
    }
}
