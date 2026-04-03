<?php

declare(strict_types=1);

namespace MiPress\Forms\Models;

use MiPress\Forms\Enums\FormFieldType;

class FormField
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function contactTemplate(): array
    {
        return [
            [
                'handle' => 'name',
                'type' => FormFieldType::Text->value,
                'label' => 'Jméno a příjmení',
                'required' => true,
                'config' => [
                    'placeholder' => 'Jan Novák',
                ],
                'order' => 1,
            ],
            [
                'handle' => 'email',
                'type' => FormFieldType::Email->value,
                'label' => 'Email',
                'required' => true,
                'config' => [],
                'order' => 2,
            ],
            [
                'handle' => 'phone',
                'type' => FormFieldType::Phone->value,
                'label' => 'Telefon',
                'required' => false,
                'config' => [],
                'order' => 3,
            ],
            [
                'handle' => 'message',
                'type' => FormFieldType::Textarea->value,
                'label' => 'Zpráva',
                'required' => true,
                'config' => [
                    'rows' => 5,
                ],
                'order' => 4,
            ],
        ];
    }
}
