<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PrivateClass;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\PrivateClassResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\PrivateClassResource\RelationManagers\PrivateClassItemsRelationManager;
use App\Forms\Components\MetadataForm;
use App\Forms\Components\PriceForm;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Builder;

/**
 * PrivateClassResource
 * 
 * Filament resource for managing Private Classes with multi-language support.
 * Provides a clean, user-friendly interface for CRUD operations on private classes.
 * 
 * Features:
 * - Multi-language support (English/Indonesian)
 * - Clean code architecture with proper separation of concerns
 * - Interactive UI with real-time validation
 * - Comprehensive filtering and search capabilities
 * - Image upload with S3 integration
 * - Relationship management for private class items
 * 
 * @package App\Filament\Resources
 * @author LMS Development Team
 * @version 1.0.0
 */
class PrivateClassResource extends Resource
{
    protected static ?string $model = PrivateClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    /**
     * Get the navigation group for this resource
     * Supports multi-language based on current locale
     */
    public static function getNavigationGroup(): ?string
    {
        return __('private_class.navigation_group');
    }

    /**
     * Get the model label for this resource
     * Supports multi-language based on current locale
     */
    public static function getModelLabel(): string
    {
        return __('private_class.singular');
    }

    /**
     * Get the plural model label for this resource
     * Supports multi-language based on current locale
     */
    public static function getPluralModelLabel(): string
    {
        return __('private_class.plural');
    }

    /**
     * Define the form schema for creating/editing private classes
     * 
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Section
                Section::make(__('private_class.sections.basic_info'))
                    ->description('Enter the basic information for the private class')
                    ->schema([
                        // Image Upload
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('private_class.fields.image'))
                            ->collection('images')
                            ->disk('s3_public')
                            ->image()
                            ->previewable()
                            ->columnSpanFull()
                            ->required()
                            ->helperText('Upload an image for the private class (JPG, PNG, max 2MB)'),
                        
                        // Name Field
                        TextInput::make('name')
                            ->label(__('private_class.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            })
                            ->helperText('Enter a unique name for the private class'),
                        
                        // Slug Field
                        TextInput::make('slug')
                            ->label(__('private_class.fields.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->rules(['alpha_dash'])
                            ->helperText('URL-friendly version of the name (auto-generated)'),
                        
                        // Short Description
                        TextInput::make('short_description')
                            ->label(__('private_class.fields.short_description'))
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Brief description shown in listings'),
                        
                        // Full Description
                        RichEditor::make('description')
                            ->label(__('private_class.fields.description'))
                            ->fileAttachmentsDisk('s3')
                            ->fileAttachmentsDirectory('attachments')
                            ->columnSpanFull()
                            ->helperText('Detailed description with rich text formatting'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                // Schedule Section
                Section::make(__('private_class.sections.schedule'))
                    ->description('Set the schedule and capacity for the private class')
                    ->schema([
                        // Duration
                        TextInput::make('duration')
                            ->label(__('private_class.fields.duration'))
                            ->numeric()
                            ->minValue(1)
                            ->suffix('minutes')
                            ->helperText('Duration in minutes'),
                        
                        // Duration Units
                        TextInput::make('duration_units')
                            ->label(__('private_class.fields.duration_units'))
                            ->default('minutes')
                            ->maxLength(255)
                            ->helperText('Unit of time measurement'),
                        
                        // Max Participants
                        TextInput::make('max_participants')
                            ->label(__('private_class.fields.max_participants'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(10)
                            ->required()
                            ->helperText('Maximum number of participants allowed'),
                        
                        // Start Date
                        DatePicker::make('start_date')
                            ->label(__('private_class.fields.start_date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('When the private class begins'),
                        
                        // End Date
                        DatePicker::make('end_date')
                            ->label(__('private_class.fields.end_date'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('start_date')
                            ->helperText('When the private class ends'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                // Status and Payment Section
                Section::make('Status & Payment')
                    ->description('Configure the status and payment settings')
                    ->schema([
                        // Status Toggle
                        Toggle::make('status')
                            ->label(__('private_class.fields.status'))
                            ->required()
                            ->default(true)
                            ->helperText('Enable to make this private class available'),
                        
                        // Is Paid Toggle
                        Toggle::make('is_paid')
                            ->label(__('private_class.fields.is_paid'))
                            ->live(debounce: 500, onBlur: true)
                            ->default(false)
                            ->required()
                            ->helperText('Enable if this is a paid private class'),
                        
                        // Price Form (conditional)
                        PriceForm::make(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                // Metadata Section
                MetadataForm::make(),
                
                // Timestamps (View only)
                Section::make('Timestamps')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label(__('private_class.fields.created_at'))
                            ->content(fn (PrivateClass $record): ?string => $record->created_at?->diffForHumans()),
                        
                        Placeholder::make('updated_at')
                            ->label(__('private_class.fields.updated_at'))
                            ->content(fn (PrivateClass $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columns(2)
                    ->hiddenOn('create')
                    ->collapsible(),
            ]);
    }

    /**
     * Define the table schema for listing private classes
     * 
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Image Column
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('private_class.fields.image'))
                    ->collection('images')
                    ->disk('s3_public')
                    ->size(60)
                    ->circular(),
                
                // Name Column with Description
                TextColumn::make('name')
                    ->label(__('private_class.fields.name'))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->description(fn(PrivateClass $record): string | null => 
                        Str::limit($record->short_description, 50)
                    )
                    ->searchable()
                    ->sortable(),
                
                // Schedule Information
                TextColumn::make('schedule')
                    ->label('Schedule')
                    ->getStateUsing(function (PrivateClass $record): string {
                        $start = $record->start_date?->format('d/m/Y') ?? 'TBD';
                        $end = $record->end_date?->format('d/m/Y') ?? 'TBD';
                        return "{$start} - {$end}";
                    })
                    ->badge()
                    ->color('info'),
                
                // Participants
                TextColumn::make('max_participants')
                    ->label(__('private_class.fields.max_participants'))
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                
                // Payment Status
                BadgeColumn::make('is_paid')
                    ->label(__('private_class.fields.is_paid'))
                    ->getStateUsing(fn (PrivateClass $record): string => 
                        $record->is_paid ? __('private_class.payment.paid') : __('private_class.payment.free')
                    )
                    ->colors([
                        'success' => __('private_class.payment.paid'),
                        'secondary' => __('private_class.payment.free'),
                    ]),
                
                // Status
                IconColumn::make('status')
                    ->label(__('private_class.fields.status'))
                    ->boolean()
                    ->sortable(),
                
                // Timestamps
                TextColumn::make('created_at')
                    ->label(__('private_class.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label(__('private_class.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Status Filter
                TernaryFilter::make('status')
                    ->label(__('private_class.fields.status'))
                    ->boolean()
                    ->trueLabel(__('private_class.status.active'))
                    ->falseLabel(__('private_class.status.inactive'))
                    ->native(false),
                
                // Payment Filter
                TernaryFilter::make('is_paid')
                    ->label(__('private_class.fields.is_paid'))
                    ->boolean()
                    ->trueLabel(__('private_class.payment.paid'))
                    ->falseLabel(__('private_class.payment.free'))
                    ->native(false),
                
                // Date Range Filter
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('From Date'),
                        DatePicker::make('end_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('private_class.actions.view')),
                Tables\Actions\EditAction::make()
                    ->label(__('private_class.actions.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('private_class.actions.delete'))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    /**
     * Get the relation managers for this resource
     * 
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            PrivateClassItemsRelationManager::class,
        ];
    }

    /**
     * Get the pages for this resource
     * 
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrivateClasses::route('/'),
            'create' => Pages\CreatePrivateClass::route('/create'),
            'view' => Pages\ViewPrivateClass::route('/{record}'),
            'edit' => Pages\EditPrivateClass::route('/{record}/edit'),
        ];
    }

    /**
     * Get the global search result details
     * 
     * @return array
     */
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            __('private_class.fields.short_description') => $record->short_description,
            __('private_class.fields.status') => $record->status ? __('private_class.status.active') : __('private_class.status.inactive'),
            __('private_class.fields.is_paid') => $record->is_paid ? __('private_class.payment.paid') : __('private_class.payment.free'),
        ];
    }

    /**
     * Get the global search result actions
     * 
     * @return array
     */
    public static function getGlobalSearchResultActions($record): array
    {
        return [
            Tables\Actions\ViewAction::make('view')
                ->label(__('private_class.actions.view'))
                ->url(static::getUrl('view', ['record' => $record])),
            Tables\Actions\EditAction::make('edit')
                ->label(__('private_class.actions.edit'))
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }
}