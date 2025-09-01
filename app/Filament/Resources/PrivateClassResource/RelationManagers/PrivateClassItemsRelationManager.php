<?php

namespace App\Filament\Resources\PrivateClassResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Course;
use App\Models\PrivateClassItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Model;

/**
 * PrivateClassItemsRelationManager
 *
 * Manages the relationship between Private Classes and their items (courses).
 * Provides a clean interface for adding, editing, and removing items from private classes.
 *
 * @package App\Filament\Resources\PrivateClassResource\RelationManagers
 */
class PrivateClassItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'privateClassItems';

    protected static ?string $recordTitleAttribute = 'model_id';

    /**
     * Define the form schema for creating/editing private class items
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        // Model Type Selection
                        Select::make('model_type')
                            ->label('Content Type')
                            ->options([
                                Course::class => 'Course',
                                // Add other model types as needed
                            ])
                            ->default(Course::class)
                            ->required()
                            ->live()
                            ->helperText('Select the type of content to add to this private class'),

                        // Model Selection (Dynamic based on model_type)
                        Select::make('model_id')
                            ->label('Content')
                            ->options(function (Forms\Get $get) {
                                $modelType = $get('model_type');

                                if ($modelType === Course::class) {
                                    return Course::query()
                                        ->where('status', true)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }

                                return [];
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select the specific content item'),

                        // Order
                        TextInput::make('order')
                            ->label('Order')
                            ->numeric()
                            ->default(function () {
                                // Auto-increment order based on existing items
                                $maxOrder = $this->getOwnerRecord()
                                    ->privateClassItems()
                                    ->max('order') ?? 0;
                                return $maxOrder + 1;
                            })
                            ->minValue(1)
                            ->required()
                            ->helperText('Display order of this item in the private class'),
                    ]),
            ]);
    }

    /**
     * Define the table schema for listing private class items
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('model_id')
            ->columns([
                // Order Column
                TextColumn::make('order')
                    ->label('Order')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Content Type Column
                BadgeColumn::make('model_type')
                    ->label('Type')
                    ->getStateUsing(function (PrivateClassItem $record): string {
                        return class_basename($record->model_type);
                    })
                    ->colors([
                        'primary' => 'Course',
                        'secondary' => 'Bundle',
                    ]),

                // Content Name Column
                TextColumn::make('model.name')
                    ->label('Content Name')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),

                // Content Status (if applicable)
                TextColumn::make('model.status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (PrivateClassItem $record): string {
                        return $record->model?->status ? 'Active' : 'Inactive';
                    })
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Inactive',
                    ]),

                // Content Duration (if applicable)
                TextColumn::make('model.duration')
                    ->label('Duration')
                    ->getStateUsing(function (PrivateClassItem $record): ?string {
                        if ($record->model && isset($record->model->duration)) {
                            return $record->model->duration . ' ' . ($record->model->duration_units ?? 'minutes');
                        }
                        return null;
                    })
                    ->placeholder('N/A'),

                // Timestamps
                TextColumn::make('created_at')
                    ->label('Added At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter by Model Type
                Tables\Filters\SelectFilter::make('model_type')
                    ->label('Content Type')
                    ->options([
                        Course::class => 'Course',
                        // Add other model types as needed
                    ])
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Item')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Add Item to Private Class')
                    ->modalDescription('Select content to include in this private class')
                    ->modalWidth('lg'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Private Class Item')
                    ->modalWidth('lg'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->striped();
    }

    /**
     * Check if the relation manager is read only
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return false;
    }
}
