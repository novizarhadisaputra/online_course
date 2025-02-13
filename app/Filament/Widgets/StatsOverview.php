<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\Course;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $views = View::count();
        $courses = Course::select(['id'])->count();
        $transactions = Transaction::select(['id'])->count();

        return [
            Stat::make('Courses', $courses),
            Stat::make('Views', $views),
            Stat::make('Transactions', $transactions),
        ];
    }
}
