<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $events = Event::select(['id'])->count();
        $eventActive = Event::select(['id'])->where(['status' => 1])->count();
        $eventNonActive = Event::select(['id'])->where(['status' => 0])->count();

        return [
            Stat::make('Total Events', $events),
            Stat::make('Total Event Active', $eventActive),
            Stat::make('Total Event Non Active', $eventNonActive),
        ];
    }
}
