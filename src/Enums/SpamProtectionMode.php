<?php

declare(strict_types=1);

namespace MiPress\Forms\Enums;

enum SpamProtectionMode: string
{
    case Honeypot = 'honeypot';
    case Recaptcha = 'recaptcha';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Honeypot => __('mipress-forms::admin.enums.spam_protection_mode.honeypot'),
            self::Recaptcha => __('mipress-forms::admin.enums.spam_protection_mode.recaptcha'),
            self::Both => __('mipress-forms::admin.enums.spam_protection_mode.both'),
        };
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

    public function usesHoneypot(): bool
    {
        return in_array($this, [self::Honeypot, self::Both], true);
    }

    public function usesRecaptcha(): bool
    {
        return in_array($this, [self::Recaptcha, self::Both], true);
    }
}
