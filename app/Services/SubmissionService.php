<?php

namespace App\Services;

use App\Mail\SubmissionMail;
use App\Models\FormSubmission;
use App\Settings\MailSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SubmissionService
{
    private const RECIPIENTS = [
        'contact' => 'recipients_contact',
        'careers' => 'recipients_careers',
        'project' => 'recipients_project',
        'investor' => 'recipients_investor',
    ];

    public function __construct(private MailSettings $mail) {}

    /** Guarda el envío (siempre primero) y programa la notificación por email para después de responder. */
    public function store(Request $request, string $type, array $fields, bool $notify = true): FormSubmission
    {
        $submission = FormSubmission::create($fields + [
            'type' => $type,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'recaptcha_score' => $request->attributes->get('recaptcha_score'),
            'is_spam_suspect' => (bool) $request->attributes->get('spam_suspect', false),
        ]);

        if ($notify) {
            dispatch(fn () => $this->notify($submission->id))->afterResponse();
        }

        return $submission;
    }

    public function notify(int $id): void
    {
        $submission = FormSubmission::find($id);
        $recipients = array_filter($this->mail->{self::RECIPIENTS[$submission?->type] ?? ''} ?? []);
        if (! $submission || ! $recipients) {
            return;
        }
        try {
            Mail::to($recipients)->send(new SubmissionMail($submission));
            $submission->update(['mail_sent_at' => now(), 'mail_error' => null]);
        } catch (Throwable $e) {
            Log::warning('No se pudo enviar el aviso del formulario', ['id' => $id, 'error' => $e->getMessage()]);
            $submission->update(['mail_error' => substr($e->getMessage(), 0, 2000)]);
        }
    }
}
