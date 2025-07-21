<?php

namespace App\Forms\Components;

use Filament\Forms\Get;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class PriceForm
{
    public static function make(): Fieldset
    {
        return Fieldset::make()
            ->label('Price')
            ->relationship('price')
            ->schema([
                TextInput::make('qty')
                    ->required()
                    ->maxLength(255),
                TextInput::make('units')
                    ->required()
                    ->maxLength(255),
                TextInput::make('value')
                    ->prefix('IDR')
                    ->numeric()
                    ->columnSpanFull()
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull()
                    ->required(),
            ])
            ->columns(2)
            ->visible(fn(Get $get): bool => $get('is_paid'))
            ->columnSpanFull();
    }
}
