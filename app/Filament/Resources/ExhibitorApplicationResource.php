<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendBulkEmailAction;
use App\Filament\Exports\ExhibitorApplicationExporter;
use App\Filament\Resources\ExhibitorApplicationResource\Pages;
use App\Filament\Resources\ExhibitorApplicationResource\RelationManagers;
use App\Models\ExhibitionBooth;
use App\Models\ExhibitorApplication;
use App\Models\User;
use App\Services\Notifications\NotificationDispatcher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

class ExhibitorApplicationResource extends Resource
{
    protected static ?string $model = ExhibitorApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Exhibition';

    protected static ?string $navigationLabel = 'Applications';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'exhibition_manager']);
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
                    ->content(fn (ExhibitorApplication $record) => $record->reference_no),
                Forms\Components\Placeholder::make('organization_name')
                    ->content(fn (ExhibitorApplication $record) => $record->organizationName() ?? '—'),
                Forms\Components\Placeholder::make('contact_person')
                    ->content(fn (ExhibitorApplication $record) => $record->contactPerson() ?? '—'),
                Forms\Components\Placeholder::make('email')
                    ->content(fn (ExhibitorApplication $record) => $record->email),
                Forms\Components\Select::make('preferred_booth_id')
                    ->label('Assigned Booth')
                    ->options(fn (?ExhibitorApplication $record) => ExhibitionBooth::query()
                        ->where(function ($query) use ($record) {
                            $query->where('status', 'available');

                            if ($record?->preferred_booth_id) {
                                $query->orWhere('id', $record->preferred_booth_id);
                            }
                        })
                        ->orderBy('booth_no')
                        ->pluck('booth_no', 'id'))
                    ->helperText('Assigning a booth reserves it while payment is pending; it becomes confirmed once payment is received.'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(
                        ExhibitorApplication::STATUSES,
                        array_map(fn (string $s) => ucwords(str_replace('_', ' ', $s)), ExhibitorApplication::STATUSES)
                    ))
                    ->required(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn () => User::role(['super_admin', 'admin', 'exhibition_manager'])->pluck('name', 'id')),
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
                Tables\Columns\TextColumn::make('organization')
                    ->label('Organization')
                    ->state(fn (ExhibitorApplication $record) => $record->organizationName()),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('preferredBooth.booth_no')
                    ->label('Booth'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('preferred_booth_id')
                    ->relationship('preferredBooth', 'booth_no')
                    ->label('Booth'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ExhibitorApplication::STATUSES,
                        array_map(fn (string $s) => ucwords(str_replace('_', ' ', $s)), ExhibitorApplication::STATUSES)
                    )),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('sendPaymentLink')
                    ->label('Send Payment Link')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->visible(fn (ExhibitorApplication $record) => filled($record->preferred_booth_id) && $record->status !== 'confirmed' && $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->action(function (ExhibitorApplication $record) {
                        $url = URL::signedRoute('payments.exhibition.initiate', [
                            'locale' => app()->getLocale(),
                            'application' => $record->id,
                        ]);

                        app(NotificationDispatcher::class)->notify($record, 'exhibition_payment_link', $record->email, [
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
                        ->exporter(ExhibitorApplicationExporter::class),
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
            'index' => Pages\ListExhibitorApplications::route('/'),
            'edit' => Pages\EditExhibitorApplication::route('/{record}/edit'),
        ];
    }
}
