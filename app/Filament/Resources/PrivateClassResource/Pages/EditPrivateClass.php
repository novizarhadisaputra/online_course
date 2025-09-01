<?php

namespace App\Filament\Resources\PrivateClassResource\Pages;

use App\Filament\Resources\PrivateClassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

/**
 * EditPrivateClass Page
 * 
 * Handles the editing of existing private classes with validation
 * and multi-language support.
 * 
 * @package App\Filament\Resources\PrivateClassResource\Pages
 */
class EditPrivateClass extends EditRecord
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
            Actions\ViewAction::make()
                ->label(__('private_class.actions.view'))
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->label(__('private_class.actions.delete'))
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    /**
     * Mutate form data before saving the record
     * 
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-generate slug if name changed and slug is empty
        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure required fields have default values
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
        return __('private_class.actions.edit') . ': ' . $this->getRecord()->name;
    }

    /**
     * Get the breadcrumb for this page
     * 
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return __('private_class.actions.edit');
    }

    /**
     * Get the success notification message
     * 
     * @return string
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return __('private_class.messages.updated');
    }

    /**
     * Redirect after successful update
     * 
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}