<?php

namespace App\Filament\Resources\PrivateClassResource\Pages;

use App\Filament\Resources\PrivateClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * ListPrivateClasses Page
 * 
 * Handles the listing of private classes with filtering capabilities
 * and multi-language support.
 * 
 * @package App\Filament\Resources\PrivateClassResource\Pages
 */
class ListPrivateClasses extends ListRecords
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
            Actions\CreateAction::make()
                ->label(__('private_class.actions.create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Get the tabs for filtering records
     * 
     * @return array
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All'))
                ->badge($this->getModel()::count()),
            
            'active' => Tab::make(__('private_class.status.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', true))
                ->badge($this->getModel()::where('status', true)->count()),
            
            'inactive' => Tab::make(__('private_class.status.inactive'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', false))
                ->badge($this->getModel()::where('status', false)->count()),
            
            'paid' => Tab::make(__('private_class.payment.paid'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', true))
                ->badge($this->getModel()::where('is_paid', true)->count()),
            
            'free' => Tab::make(__('private_class.payment.free'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_paid', false))
                ->badge($this->getModel()::where('is_paid', false)->count()),
        ];
    }

    /**
     * Get the page title
     * 
     * @return string
     */
    public function getTitle(): string
    {
        return __('private_class.plural');
    }

    /**
     * Get the breadcrumb for this page
     * 
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return __('private_class.plural');
    }
}