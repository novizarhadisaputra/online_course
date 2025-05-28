<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Products extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $navigationGroup = 'Master Data';

    protected static bool $shouldRegisterNavigation = true;
}
