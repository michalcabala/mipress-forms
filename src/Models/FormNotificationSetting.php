<?php

declare(strict_types=1);

namespace MiPress\Forms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MiPress\Forms\Enums\FormNotificationPreference;

class FormNotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'form_notification_settings';

    protected $fillable = [
        'user_id',
        'preference',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'preference' => FormNotificationPreference::class,
    ];
}
