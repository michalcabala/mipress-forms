<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Resources\FormResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use MiPress\Core\Filament\Resources\Concerns\HasContextualCrudNotifications;
use MiPress\Forms\Filament\Resources\FormResource;

class EditForm extends EditRecord
{
    use HasContextualCrudNotifications;

    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
