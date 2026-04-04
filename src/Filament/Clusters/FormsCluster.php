<?php

declare(strict_types=1);

namespace MiPress\Forms\Filament\Clusters;

use Filament\Clusters\Cluster;

class FormsCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Formuláře';

    protected static ?string $label = 'Formuláře';

    protected static ?string $pluralLabel = 'Formuláře';

    protected static ?int $navigationSort = 1;
}
