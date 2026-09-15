<?php

namespace App\Filament\Resources\CampusProgramResource\RelationManagers;

use App\Models\ActivationLead;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivationLeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'activationLeads';

    protected static ?string $title = 'Leads';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str($state)->replace('_', ' ')->title()),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        \Filament\Forms\Components\Placeholder::make('message')
                            ->content(fn (ActivationLead $record) => $record->message ?: '—'),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
