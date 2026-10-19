<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FormSubmission extends Model
{
    public const TYPES = [
        'contact' => 'Contacto',
        'careers' => 'Trabaja con nosotros',
        'investor' => 'Investor Pass',
        'project' => 'Consulta de proyecto',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_spam_suspect' => 'boolean',
            'mail_sent_at' => 'datetime',
            'read_at' => 'datetime',
            'recaptcha_score' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $submission) {
            if ($submission->cv_path) {
                Storage::disk('local')->delete($submission->cv_path);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
