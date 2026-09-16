<?php

namespace App\Filament\Resources\SponsorshipEnquiryResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    // Payment has no Filament resource/policy of its own, so Filament's
    // policy-based authorization would deny it by default (see the same fix
    // on AwardNominationResource's ReviewsRelationManager in Phase 7).
    protected static bool $shouldSkipAuthorization = true;

    // A handful of payment attempts at most — no reason to lazy-load, and
    // eager rendering means it's genuinely visible (including in tests).
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('gateway_txn_id')
            ->columns([
                Tables\Columns\TextColumn::make('gateway_txn_id')
                    ->label('Transaction ID')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BDT'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Initiated')
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
