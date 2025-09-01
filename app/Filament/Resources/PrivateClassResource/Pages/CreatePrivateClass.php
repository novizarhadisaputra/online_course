<?php

namespace App\Filament\Resources\PrivateClassResource\Pages;

use App\Filament\Resources\PrivateClassResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

/**
 * CreatePrivateClass Page
 * 
 * Handles the creation of new private classes with automatic slug generation
 * and multi-language support.
 * 
 * @package App\Filament\Resources\PrivateClassResource\Pages
 */
class CreatePrivateClass extends CreateRecord
{
    protected static string $resource = PrivateClassResource::class;

    /**
     * Mutate form data before creating the record
     * 
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default values
        $data['status'] = $data['status'] ?? true;
        $data['is_paid'] = $data['is_paid'] ?? false;
        $data['duration_units'] = $data['duration_units'] ?? 'minutes';
        $data['max_participants'] = $data['max_participants'] ?? 10;

        return $data;
    }

    /**
     * Get the page title
     * 
     * @return string
     */
    public function getTitle(): string
    {
        return __('private_class.actions.create');
    }

    /**
     * Get the breadcrumb for this page
     * 
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return __('private_class.actions.create');
    }

    /**
     * Get the success notification message
     * 
     * @return string
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('private_class.messages.created');
    }

    /**
     * Redirect after successful creation
     * 
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}