<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendBulkEmailAction;
use App\Filament\Exports\SponsorshipEnquiryExporter;
use App\Filament\Resources\SponsorshipEnquiryResource\Pages;
use App\Filament\Resources\SponsorshipEnquiryResource\RelationManagers;
use App\Models\SponsorshipEnquiry;
use App\Models\User;
use App\Services\Notifications\NotificationDispatcher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

class SponsorshipEnquiryResource extends Resource
{
    protected static ?string $model = SponsorshipEnquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Sponsorship';

    protected static ?string $navigationLabel = 'Enquiries';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'sponsorship_manager']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Placeholder::make('reference_no')
                    ->content(fn (SponsorshipEnquiry $record) => $record->reference_no),
                Forms\Components\Placeholder::make('company_name')
                    ->content(fn (SponsorshipEnquiry $record) => $record->companyName() ?? '—'),
                Forms\Components\Placeholder::make('contact_person')
                    ->content(fn (SponsorshipEnquiry $record) => $record->contactPerson() ?? '—'),
                Forms\Components\Placeholder::make('email')
                    ->content(fn (SponsorshipEnquiry $record) => $record->email),
                Forms\Components\Placeholder::make('callback_requested')
                    ->label('Callback Requested')
                    ->content(fn (SponsorshipEnquiry $record) => $record->callback_requested_at
                        ? 'Yes, requested '.$record->callback_requested_at->diffForHumans()
                        : 'No'),
                Forms\Components\Placeholder::make('preferred_contact_time')
                    ->content(fn (SponsorshipEnquiry $record) => $record->preferred_contact_time ?? '—'),
                Forms\Components\Select::make('sponsor_tier_id')
                    ->label('Sponsorship Tier')
                    ->relationship('sponsorTier', 'name'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(
                        SponsorshipEnquiry::STATUSES,
                        array_map(fn (string $s) => ucwords(str_replace('_', ' ', $s)), SponsorshipEnquiry::STATUSES)
                    ))
                    ->required(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn () => User::role(['super_admin', 'admin', 'sponsorship_manager'])->pluck('name', 'id')),
                Forms\Components\Textarea::make('internal_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_no')
                    ->searchable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->state(fn (SponsorshipEnquiry $record) => $record->companyName()),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('sponsorTier.name')
                    ->label('Tier'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\IconColumn::make('callback_requested_at')
                    ->label('Callback?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sponsor_tier_id')
                    ->relationship('sponsorTier', 'name')
                    ->label('Tier'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        SponsorshipEnquiry::STATUSES,
                        array_map(fn (string $s) => ucwords(str_replace('_', ' ', $s)), SponsorshipEnquiry::STATUSES)
                    )),
                Tables\Filters\TernaryFilter::make('callback_requested_at')
                    ->label('Callback Requested')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('sendPaymentLink')
                    ->label('Send Payment Link')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->visible(fn (SponsorshipEnquiry $record) => filled($record->sponsor_tier_id) && $record->status !== 'closed')
                    ->requiresConfirmation()
                    ->action(function (SponsorshipEnquiry $record) {
                        $url = URL::signedRoute('payments.sponsorship.initiate', [
                            'locale' => app()->getLocale(),
                            'enquiry' => $record->id,
                        ]);

                        app(NotificationDispatcher::class)->notify($record, 'sponsorship_payment_link', $record->email, [
                            'name' => $record->contactPerson() ?? '',
                            'reference_no' => $record->reference_no,
                            'payment_link' => $url,
                        ]);

                        Notification::make()
                            ->title('Payment link emailed to '.$record->email)
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(SponsorshipEnquiryExporter::class),
                    SendBulkEmailAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsorshipEnquiries::route('/'),
            'edit' => Pages\EditSponsorshipEnquiry::route('/{record}/edit'),
        ];
    }
}
