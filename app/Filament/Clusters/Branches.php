<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Branches extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Branches';

    protected static ?string $navigationGroup = 'Stock Management';

    protected static bool $shouldRegisterNavigation = true;
}
