<?php

declare(strict_types=1);

namespace MiPress\Forms\Enums;

enum FormFieldType: string
{
    case Text = 'text';
    case Email = 'email';
    case Phone = 'phone';
    case Textarea = 'textarea';
    case Select = 'select';
    case Checkbox = 'checkbox';
    case Radio = 'radio';
    case File = 'file';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Email => 'Email',
            self::Phone => 'Telefon',
            self::Textarea => 'Textová oblast',
            self::Select => 'Výběr',
            self::Checkbox => 'Zaškrtávací pole',
            self::Radio => 'Přepínač',
            self::File => 'Soubor',
            self::Hidden => 'Skryté pole',
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
}
