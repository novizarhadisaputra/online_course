<?php

namespace App\Filament\Resources\PrivateClassResource\Pages;

use App\Filament\Resources\PrivateClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

/**
 * ViewPrivateClass Page
 * 
 * Handles the detailed view of private classes with comprehensive
 * information display and multi-language support.
 * 
 * @package App\Filament\Resources\PrivateClassResource\Pages
 */
class ViewPrivateClass extends ViewRecord
{
    protected static string $resource = PrivateClassResource::class;

    /**
     * Get the header actions for this page
     * 
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('private_class.actions.edit'))
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->label(__('private_class.actions.delete'))
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    /**
     * Get the infolist for displaying record details
     * 
     * @param Infolist $infolist
     * @return Infolist
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Basic Information Section
                Section::make(__('private_class.sections.basic_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label(__('private_class.fields.image'))
                                    ->disk('s3_public')
                                    ->collection('images')
                                    ->size(200)
                                    ->columnSpan(1),
                                
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(__('private_class.fields.name'))
                                            ->size(TextEntry\TextEntrySize::Large)
                                            ->weight('bold'),
                                        
                                        TextEntry::make('slug')
                                            ->label(__('private_class.fields.slug'))
                                            ->badge()
                                            ->color('gray'),
                                        
                                        TextEntry::make('short_description')
                                            ->label(__('private_class.fields.short_description'))
                                            ->placeholder('No short description'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                        
                        TextEntry::make('description')
                            ->label(__('private_class.fields.description'))
                            ->html()
                            ->placeholder('No description')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
                
                // Schedule & Capacity Section
                Section::make(__('private_class.sections.schedule'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('duration')
                                    ->label(__('private_class.fields.duration'))
                                    ->suffix(' minutes')
                                    ->placeholder('Not set'),
                                
                                TextEntry::make('duration_units')
                                    ->label(__('private_class.fields.duration_units'))
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('max_participants')
                                    ->label(__('private_class.fields.max_participants'))
                                    ->badge()
                                    ->color('warning'),
                                
                                TextEntry::make('participants_count')
                                    ->label('Current Participants')
                                    ->getStateUsing(fn ($record) => '0') // TODO: Implement actual count
                                    ->badge()
                                    ->color('success'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_date')
                                    ->label(__('private_class.fields.start_date'))
                                    ->date('d F Y')
                                    ->placeholder('Not set'),
                                
                                TextEntry::make('end_date')
                                    ->label(__('private_class.fields.end_date'))
                                    ->date('d F Y')
                                    ->placeholder('Not set'),
                            ]),
                    ])
                    ->collapsible(),
                
                // Status & Payment Section
                Section::make('Status & Payment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                IconEntry::make('status')
                                    ->label(__('private_class.fields.status'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                
                                IconEntry::make('is_paid')
                                    ->label(__('private_class.fields.is_paid'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-currency-dollar')
                                    ->falseIcon('heroicon-o-gift')
                                    ->trueColor('warning')
                                    ->falseColor('success'),
                            ]),
                    ])
                    ->collapsible(),
                
                // Timestamps Section
                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('private_class.fields.created_at'))
                                    ->dateTime('d F Y, H:i')
                                    ->since(),
                                
                                TextEntry::make('updated_at')
                                    ->label(__('private_class.fields.updated_at'))
                                    ->dateTime('d F Y, H:i')
                                    ->since(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Get the page title
     * 
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }

    /**
     * Get the breadcrumb for this page
     * 
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return $this->getRecord()->name;
    }
}