<?php

declare(strict_types=1);

namespace MiPress\Forms\Enums;

enum FormNotificationPreference: string
{
    case Email = 'email';
    case Database = 'database';
    case Both = 'both';
    case None = 'none';

    public function label(): string
    {
        return __('mipress-forms::admin.enums.form_notification_preference.'.$this->value);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(static fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function wantsEmail(): bool
    {
        return in_array($this, [self::Email, self::Both], true);
    }

    public function wantsDatabase(): bool
    {
        return in_array($this, [self::Database, self::Both], true);
    }
}
