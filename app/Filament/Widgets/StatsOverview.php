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
        $courses = Course::select(['id']);
        $courses = auth()->user()->hasRole(['super_admin']) ? $courses->count() : $courses->where('user_id', auth()->id())->count();
        $transactions = Transaction::select(['id'])->count();

        $stats = [
            Stat::make('Courses', $courses),
            Stat::make('Views', $views),
        ];

        if (auth()->user()->hasRole(['super_admin'])) {
            array_push($stats, Stat::make('Transactions', $transactions));
        }

        return $stats;
    }
}
