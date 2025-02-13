<?php

namespace App\Filament\Resources\NewsResource\Widgets;

use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $news = News::select(['id'])->count();
        $newsActive = News::select(['id'])->where(['status' => 1])->count();
        $newsNonActive = News::select(['id'])->where(['status' => 0])->count();

        return [
            Stat::make('Total News', $news),
            Stat::make('Total News Active', $newsActive),
            Stat::make('Total News Non Active', $newsNonActive),
        ];
    }
}
