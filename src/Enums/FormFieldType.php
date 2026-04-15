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
            self::Text => __('mipress-forms::admin.enums.form_field_type.text'),
            self::Email => __('mipress-forms::admin.enums.form_field_type.email'),
            self::Phone => __('mipress-forms::admin.enums.form_field_type.phone'),
            self::Textarea => __('mipress-forms::admin.enums.form_field_type.textarea'),
            self::Select => __('mipress-forms::admin.enums.form_field_type.select'),
            self::Checkbox => __('mipress-forms::admin.enums.form_field_type.checkbox'),
            self::Radio => __('mipress-forms::admin.enums.form_field_type.radio'),
            self::File => __('mipress-forms::admin.enums.form_field_type.file'),
            self::Hidden => __('mipress-forms::admin.enums.form_field_type.hidden'),
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
