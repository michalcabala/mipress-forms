<?php

declare(strict_types=1);

namespace MiPress\Forms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSubmissionAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_submission_attachments';

    protected $fillable = [
        'submission_id',
        'field_handle',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'submission_id');
    }
}
