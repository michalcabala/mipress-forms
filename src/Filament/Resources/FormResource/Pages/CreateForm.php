<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Resources\FormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use MiPress\Core\Filament\Resources\Concerns\HasContextualCrudNotifications;
use MiPress\Forms\Filament\Resources\FormResource;

class CreateForm extends CreateRecord
{
    use HasContextualCrudNotifications;

    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! filled($data['handle'] ?? null)) {
            $data['handle'] = Str::slug((string) ($data['title'] ?? 'form'));
        }

        return $data;
    }
}
