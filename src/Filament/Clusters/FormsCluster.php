<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use MiPress\Forms\Filament\Resources\FormSubmissionResource;

class FormsCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'fal-clipboard-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Formuláře';

    protected static ?string $navigationLabel = 'Formuláře';

    protected static ?string $label = 'Formuláře';

    protected static ?string $pluralLabel = 'Formuláře';

    protected static ?int $navigationSort = 1;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        $count = FormSubmissionResource::getUnreadSubmissionsCount();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Nepřečtené zprávy';
    }
}
