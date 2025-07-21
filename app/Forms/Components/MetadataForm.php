<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\KeyValue;

class MetadataForm
{
    public static function make(): Fieldset
    {
        return Fieldset::make()
            ->label('Metadata')
            ->relationship('metadata')
            ->schema([
                KeyValue::make('data')
                    ->columnSpanFull()
                    ->required(),
            ])
            ->columns(2)
            ->columnSpanFull();
    }
}
