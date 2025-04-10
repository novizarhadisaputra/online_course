<?php

namespace App\Filament\Resources\CourseResource\Widgets;

use App\Models\Course;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $courses = Course::select(['id']);
        if (!auth()->user()->hasRole(['super_admin'])) {
            $courses->where('user_id', auth()->id());
        }
        $courses_count = $courses->count();
        $course_active_count = $courses->where(['status' => 1])->count();
        $course_non_active_count = $courses->where(['status' => 0])->count();

        return [
            Stat::make('Total Courses', $courses_count),
            Stat::make('Total Course Active', $course_active_count),
            Stat::make('Total Course Non Active', $course_non_active_count),
        ];
    }
}
