<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\EventResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    // public function getFormSchema(): array
    // {
    //     return [
    //         Section::make()->schema([
    //             TextInput::make('name'),
    //             Grid::make()
    //                 ->schema([
    //                     DateTimePicker::make('starts_at'),
    //                     DateTimePicker::make('ends_at'),
    //                 ]),
    //         ])
    //     ];
    // }

    // protected static string $view = 'filament.widgets.calendar-widget';
    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn(Event $event) => [
                    'title' => $event->name,
                    'start' => $event->start_time,
                    'end' => $event->end_time,
                    'url' => EventResource::getUrl(name: 'view', parameters: ['record' => $event]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }
}
