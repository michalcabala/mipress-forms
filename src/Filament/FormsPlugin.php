<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use MiPress\Forms\Filament\Pages\FormNotificationSettings;
use MiPress\Forms\Filament\Resources\FormResource;
use MiPress\Forms\Filament\Resources\FormSubmissionResource;

class FormsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'mipress-forms';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            FormResource::class,
            FormSubmissionResource::class,
        ]);

        $panel->pages([
            FormNotificationSettings::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
