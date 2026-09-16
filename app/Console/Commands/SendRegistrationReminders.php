<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\NotificationLog;
use App\Models\Registration;
use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Console\Command;

class SendRegistrationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-registration-reminders {--days=7 : How many days before the summit to send the reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email/SMS a reminder to every registered attendee N days before the current edition\'s Grand Summit.';

    public function handle(NotificationDispatcher $dispatcher): int
    {
        $event = Event::current();

        if (! $event?->date_start) {
            $this->info('No current edition with a start date — nothing to do.');

            return self::SUCCESS;
        }

        // Compared as calendar dates rather than via diffInDays()'s signed
        // difference, which is easy to get backwards — this is unambiguous:
        // "is the event's start date exactly N days from today?"
        $reminderDate = now()->addDays((int) $this->option('days'))->startOfDay();

        if (! $event->date_start->copy()->startOfDay()->isSameDay($reminderDate)) {
            $this->info('Not the reminder window ('.$event->date_start->toDateString().') — nothing to do.');

            return self::SUCCESS;
        }

        $sent = 0;

        Registration::registered()
            ->where('event_id', $event->id)
            ->each(function (Registration $registration) use ($dispatcher, &$sent) {
                $alreadySent = NotificationLog::where('notifiable_type', Registration::class)
                    ->where('notifiable_id', $registration->id)
                    ->where('template_key', 'registration_reminder')
                    ->where('status', 'sent')
                    ->exists();

                if ($alreadySent) {
                    return;
                }

                $dispatcher->notify(
                    $registration,
                    'registration_reminder',
                    $registration->email,
                    ['name' => $registration->name() ?? '', 'reference_no' => $registration->reference_no],
                    $registration->mobile,
                );

                $sent++;
            });

        $this->info("Sent {$sent} reminder(s).");

        return self::SUCCESS;
    }
}
