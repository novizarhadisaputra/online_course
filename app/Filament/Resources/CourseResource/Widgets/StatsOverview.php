<?php

namespace App\Filament\Resources\CourseResource\Widgets;

use App\Models\Course;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $courses = Course::select(['id'])->count();
        $courseActive = Course::select(['id'])->where(['status' => 1])->count();
        $courseNonActive = Course::select(['id'])->where(['status' => 0])->count();

        return [
            Stat::make('Total Courses', $courses),
            Stat::make('Total Course Active', $courseActive),
            Stat::make('Total Course Non Active', $courseNonActive),
        ];
    }
}
