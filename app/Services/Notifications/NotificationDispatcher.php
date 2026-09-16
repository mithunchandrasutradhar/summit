<?php

namespace App\Services\Notifications;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Services\Sms\SmsGateway;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationDispatcher
{
    public function __construct(
        protected SmsGateway $smsGateway,
    ) {}

    /**
     * Send a templated email (and optional SMS) to a submitter, logging the
     * outcome of each attempted channel against the given notifiable model.
     */
    public function notify(
        Model $notifiable,
        string $templateKey,
        string $toEmail,
        array $placeholders = [],
        ?string $toPhone = null,
    ): void {
        $this->sendEmail($notifiable, $templateKey, $toEmail, $placeholders);

        if ($toPhone && app(GeneralSettings::class)->sms_enabled) {
            $this->sendSms($notifiable, $templateKey, $toPhone, $placeholders);
        }
    }

    /**
     * Email-only counterpart to notify() for alerting internal staff (e.g.
     * a Sponsorship/Exhibition Manager) about a new lead, rather than the
     * public submitter. Each recipient's send is logged against the same
     * notifiable record, alongside the submitter-facing confirmation.
     *
     * @param  Collection<int, Model&\Illuminate\Contracts\Auth\Authenticatable>  $recipients
     */
    public function notifyStaff(
        Model $notifiable,
        string $templateKey,
        Collection $recipients,
        array $placeholders = [],
    ): void {
        foreach ($recipients as $recipient) {
            if (! $recipient->email) {
                continue;
            }

            $this->sendEmail($notifiable, $templateKey, $recipient->email, $placeholders);
        }
    }

    protected function sendEmail(Model $notifiable, string $templateKey, string $toEmail, array $placeholders): void
    {
        $template = EmailTemplate::where('key', $templateKey)->first();

        if (! $template) {
            $this->log($notifiable, 'email', $templateKey, 'failed', 'No email template found for key: '.$templateKey);

            return;
        }

        $rendered = $template->render($placeholders);

        try {
            Mail::to($toEmail)->queue(new TemplatedMail($rendered['subject'], $rendered['body']));
            $this->log($notifiable, 'email', $templateKey, 'sent');
        } catch (Throwable $e) {
            $this->log($notifiable, 'email', $templateKey, 'failed', $e->getMessage());
        }
    }

    protected function sendSms(Model $notifiable, string $templateKey, string $toPhone, array $placeholders): void
    {
        $template = EmailTemplate::where('key', $templateKey.'_sms')->first();
        $message = $template
            ? $template->render($placeholders)['body']
            : ($placeholders['reference_no'] ?? '').' — '.config('app.name');

        try {
            $sent = $this->smsGateway->send($toPhone, strip_tags($message));
            $this->log($notifiable, 'sms', $templateKey, $sent ? 'sent' : 'failed');
        } catch (Throwable $e) {
            $this->log($notifiable, 'sms', $templateKey, 'failed', $e->getMessage());
        }
    }

    protected function log(Model $notifiable, string $channel, string $templateKey, string $status, ?string $error = null): void
    {
        NotificationLog::create([
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'channel' => $channel,
            'template_key' => $templateKey,
            'status' => $status,
            'sent_at' => $status === 'sent' ? now() : null,
            'error' => $error,
        ]);
    }
}
