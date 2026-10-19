<?php

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SubmissionMail extends Mailable
{
    public function __construct(public FormSubmission $submission) {}

    public function envelope(): Envelope
    {
        $type = FormSubmission::TYPES[$this->submission->type] ?? 'Formulario';
        $project = $this->submission->project?->name;

        return new Envelope(
            subject: "Nuevo mensaje · $type".($project ? " · $project" : '').' · Valores Real Estate',
            replyTo: $this->submission->email ? [new Address($this->submission->email, (string) $this->submission->name)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.submission', with: [
            'type' => FormSubmission::TYPES[$this->submission->type] ?? $this->submission->type,
            'rows' => array_filter([
                'Nombre' => $this->submission->name,
                'Email' => $this->submission->email,
                'Teléfono' => $this->submission->phone,
                'País' => $this->submission->country,
                'Área' => $this->submission->area,
                'Modalidad' => $this->submission->modality,
                'Proyecto' => $this->submission->project?->full_name,
                'CV adjunto' => $this->submission->cv_original_name ? $this->submission->cv_original_name.' (descargar desde el CMS)' : null,
            ]),
            'url' => url('admin/form-submissions/'.$this->submission->id),
        ]);
    }
}
