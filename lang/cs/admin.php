<?php

declare(strict_types=1);

return [
    'common' => [
        'empty' => '-',
        'yes' => 'Ano',
        'no' => 'Ne',
        'created_at_description' => 'Vytvořeno :date',
    ],
    'clusters' => [
        'forms' => [
            'navigation_group' => 'Formuláře',
            'navigation_label' => 'Formuláře',
            'label' => 'Formuláře',
            'plural_label' => 'Formuláře',
        ],
    ],
    'enums' => [
        'form_notification_preference' => [
            'email' => 'Pouze e-mail',
            'database' => 'Pouze notifikace v adminu',
            'both' => 'E-mail i notifikace v adminu',
            'none' => 'Žádné upozornění',
        ],
        'form_field_type' => [
            'text' => 'Text',
            'email' => 'Email',
            'phone' => 'Telefon',
            'textarea' => 'Textová oblast',
            'select' => 'Výběr',
            'checkbox' => 'Zaškrtávací pole',
            'radio' => 'Přepínač',
            'file' => 'Soubor',
            'hidden' => 'Skryté pole',
        ],
        'spam_protection_mode' => [
            'honeypot' => 'Honeypot',
            'recaptcha' => 'reCAPTCHA v3',
            'both' => 'Honeypot + reCAPTCHA v3',
        ],
    ],
    'resources' => [
        'form' => [
            'model_label' => 'Formulář',
            'plural_model_label' => 'Formuláře',
            'sections' => [
                'basic_settings' => 'Základní nastavení',
                'fields' => 'Pole formuláře',
                'recipients' => 'Příjemci',
                'spam_protection' => 'Ochrana proti spamu',
                'auto_reply' => 'Automatická odpověď',
                'success_message' => 'Potvrzovací zpráva',
            ],
            'fields' => [
                'template' => 'Začít ze šablony',
                'is_active' => 'Aktivní',
                'title' => 'Název',
                'handle' => 'Identifikátor',
                'description' => 'Popis',
                'form_fields' => 'Pole',
                'label' => 'Popisek',
                'type' => 'Typ',
                'required' => 'Povinné',
                'order' => 'Pořadí',
                'placeholder' => 'Zástupný text',
                'max_length' => 'Max délka',
                'rows' => 'Počet řádků',
                'max_size_mb' => 'Max velikost (MB)',
                'accepted' => 'Povolené přípony',
                'hidden_value' => 'Skrytá hodnota',
                'options' => 'Možnosti',
                'notify_users' => 'Upozornit uživatele',
                'spam_mode' => 'Režim',
                'recaptcha_site_key' => 'reCAPTCHA veřejný klíč',
                'recaptcha_secret_key' => 'reCAPTCHA tajný klíč',
                'auto_reply_enabled' => 'Zapnout automatickou odpověď',
                'auto_reply_subject' => 'Předmět',
                'auto_reply_body' => 'Text zprávy',
                'success_message' => 'Zpráva po odeslání',
            ],
            'options' => [
                'template' => [
                    'none' => 'Prázdný formulář',
                    'contact' => 'Kontaktní formulář',
                ],
            ],
            'key_value' => [
                'key_label' => 'Klíč',
                'value_label' => 'Popisek',
            ],
            'help' => [
                'accepted' => '.pdf,.jpg,.png',
            ],
            'defaults' => [
                'success_message' => 'Děkujeme, formulář byl odeslán.',
            ],
            'table' => [
                'columns' => [
                    'title' => 'Název',
                    'handle' => 'Identifikátor',
                    'unread_submissions' => 'Nepřečtené zprávy',
                    'is_active' => 'Aktivní',
                    'updated_at' => 'Datum',
                ],
            ],
        ],
        'form_submission' => [
            'model_label' => 'Odeslaná zpráva',
            'plural_model_label' => 'Odeslané zprávy',
            'unread_tooltip' => 'Nepřečtené zprávy',
            'fields' => [
                'source_form' => 'Zdrojový formulář',
                'received_at' => 'Přijato',
                'status' => 'Stav',
                'ip_address' => 'IP adresa',
                'user_agent' => 'Uživatelský agent',
                'message_content' => 'Obsah zprávy',
                'attachments' => 'Přílohy',
                'message_preview' => 'Náhled zprávy',
            ],
            'states' => [
                'read' => 'Přečteno',
                'unread' => 'Nepřečteno',
            ],
            'actions' => [
                'mark_read' => 'Označit jako přečtené',
                'mark_unread' => 'Označit jako nepřečtené',
            ],
        ],
    ],
    'pages' => [
        'form_notification_settings' => [
            'navigation_label' => 'Nastavení notifikací',
            'title' => 'Notifikace zpráv z formulářů',
            'section' => 'Upozornění na nové zprávy',
            'description' => 'Zvolte, jak chcete dostávat upozornění na nově odeslané zprávy z formulářů.',
            'field' => 'Způsob upozornění',
            'saved_title' => 'Nastavení uloženo',
            'saved_body' => 'Nový způsob upozornění na formuláře: :preference.',
        ],
    ],
];
