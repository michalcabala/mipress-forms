<?php

declare(strict_types=1);

return [
    'common' => [
        'empty' => '-',
        'yes' => 'Yes',
        'no' => 'No',
        'created_at_description' => 'Created :date',
    ],
    'clusters' => [
        'forms' => [
            'navigation_group' => 'Forms',
            'navigation_label' => 'Forms',
            'label' => 'Forms',
            'plural_label' => 'Forms',
        ],
    ],
    'enums' => [
        'form_notification_preference' => [
            'email' => 'Email only',
            'database' => 'Admin notifications only',
            'both' => 'Email and admin notifications',
            'none' => 'No notifications',
        ],
        'form_field_type' => [
            'text' => 'Text',
            'email' => 'Email',
            'phone' => 'Phone',
            'textarea' => 'Textarea',
            'select' => 'Select',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio button',
            'file' => 'File',
            'hidden' => 'Hidden field',
        ],
        'spam_protection_mode' => [
            'honeypot' => 'Honeypot',
            'recaptcha' => 'reCAPTCHA v3',
            'both' => 'Honeypot + reCAPTCHA v3',
        ],
    ],
    'resources' => [
        'form' => [
            'model_label' => 'Form',
            'plural_model_label' => 'Forms',
            'sections' => [
                'basic_settings' => 'Basic settings',
                'fields' => 'Form fields',
                'recipients' => 'Recipients',
                'spam_protection' => 'Spam protection',
                'auto_reply' => 'Auto reply',
                'success_message' => 'Confirmation message',
            ],
            'fields' => [
                'template' => 'Start from template',
                'is_active' => 'Active',
                'title' => 'Title',
                'handle' => 'Identifier',
                'description' => 'Description',
                'form_fields' => 'Fields',
                'label' => 'Label',
                'type' => 'Type',
                'required' => 'Required',
                'order' => 'Order',
                'placeholder' => 'Placeholder',
                'max_length' => 'Max length',
                'rows' => 'Rows',
                'max_size_mb' => 'Max size (MB)',
                'accepted' => 'Allowed extensions',
                'hidden_value' => 'Hidden value',
                'options' => 'Options',
                'notify_users' => 'Notify users',
                'spam_mode' => 'Mode',
                'recaptcha_site_key' => 'reCAPTCHA site key',
                'recaptcha_secret_key' => 'reCAPTCHA secret key',
                'auto_reply_enabled' => 'Enable auto reply',
                'auto_reply_subject' => 'Subject',
                'auto_reply_body' => 'Message body',
                'success_message' => 'Post-submit message',
            ],
            'options' => [
                'template' => [
                    'none' => 'Blank form',
                    'contact' => 'Contact form',
                ],
            ],
            'key_value' => [
                'key_label' => 'Key',
                'value_label' => 'Label',
            ],
            'help' => [
                'accepted' => '.pdf,.jpg,.png',
            ],
            'defaults' => [
                'success_message' => 'Thank you, the form has been sent.',
            ],
            'table' => [
                'columns' => [
                    'title' => 'Title',
                    'handle' => 'Identifier',
                    'unread_submissions' => 'Unread messages',
                    'is_active' => 'Active',
                    'updated_at' => 'Date',
                ],
            ],
        ],
        'form_submission' => [
            'model_label' => 'Submitted message',
            'plural_model_label' => 'Submitted messages',
            'unread_tooltip' => 'Unread messages',
            'fields' => [
                'source_form' => 'Source form',
                'received_at' => 'Received',
                'status' => 'Status',
                'ip_address' => 'IP address',
                'user_agent' => 'User agent',
                'message_content' => 'Message content',
                'attachments' => 'Attachments',
                'message_preview' => 'Message preview',
            ],
            'states' => [
                'read' => 'Read',
                'unread' => 'Unread',
            ],
            'actions' => [
                'mark_read' => 'Mark as read',
                'mark_unread' => 'Mark as unread',
            ],
        ],
    ],
    'pages' => [
        'form_notification_settings' => [
            'navigation_label' => 'Notification settings',
            'title' => 'Form message notifications',
            'section' => 'Notifications for new messages',
            'description' => 'Choose how you want to receive notifications about newly submitted form messages.',
            'field' => 'Notification channel',
            'saved_title' => 'Settings saved',
            'saved_body' => 'New form notification preference: :preference.',
        ],
    ],
];
