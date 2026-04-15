<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use MiPress\Forms\Filament\Resources\FormSubmissionResource;

class FormsCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'fal-clipboard-list';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?string $navigationLabel = null;

    protected static ?string $label = null;

    protected static ?string $pluralLabel = null;

    protected static ?int $navigationSort = 1;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('mipress-forms::admin.clusters.forms.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('mipress-forms::admin.clusters.forms.navigation_label');
    }

    public static function getLabel(): string
    {
        return __('mipress-forms::admin.clusters.forms.label');
    }

    public static function getPluralLabel(): string
    {
        return __('mipress-forms::admin.clusters.forms.plural_label');
    }

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
        return __('mipress-forms::admin.resources.form_submission.unread_tooltip');
    }
}
