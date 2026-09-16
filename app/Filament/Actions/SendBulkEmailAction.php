<?php

namespace App\Filament\Actions;

use App\Models\EmailTemplate;
use App\Services\Notifications\NotificationDispatcher;
use Closure;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * A reusable "send a templated email to the selected/filtered records" bulk
 * action — the brief's "bulk email capability" (plan §17), available on any
 * resource with an EmailTemplate-backed workflow. Defaults to reading a
 * plain `email` column; override via ->recipientEmail() for models that
 * store it under a different name (e.g. AwardNomination's nominee_email).
 */
class SendBulkEmailAction extends BulkAction
{
    protected Closure $recipientEmailResolver;

    protected Closure $placeholdersResolver;

    public static function make(?string $name = null): static
    {
        $action = parent::make($name ?? 'sendBulkEmail');

        return $action
            ->label('Send Bulk Email')
            ->icon('heroicon-o-envelope')
            ->color('gray')
            ->recipientEmail(fn (Model $record) => $record->email)
            ->placeholders(fn (Model $record) => [])
            ->deselectRecordsAfterCompletion()
            ->form([
                Forms\Components\Select::make('email_template_id')
                    ->label('Email Template')
                    ->options(fn () => EmailTemplate::query()->pluck('subject', 'id'))
                    ->required()
                    ->searchable(),
            ])
            ->requiresConfirmation()
            ->action(function (Collection $records, array $data, SendBulkEmailAction $action) {
                $template = EmailTemplate::find($data['email_template_id']);

                if (! $template) {
                    return;
                }

                $sent = 0;

                foreach ($records as $record) {
                    $email = ($action->recipientEmailResolver)($record);

                    if (! $email) {
                        continue;
                    }

                    app(NotificationDispatcher::class)->notify(
                        $record,
                        $template->key,
                        $email,
                        ($action->placeholdersResolver)($record),
                    );

                    $sent++;
                }

                Notification::make()
                    ->title("Queued {$sent} email(s)")
                    ->success()
                    ->send();
            });
    }

    public function recipientEmail(Closure $resolver): static
    {
        $this->recipientEmailResolver = $resolver;

        return $this;
    }

    public function placeholders(Closure $resolver): static
    {
        $this->placeholdersResolver = $resolver;

        return $this;
    }
}
