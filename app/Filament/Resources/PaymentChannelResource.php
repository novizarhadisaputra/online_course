<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\PaymentChannel;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\PaymentChannelResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\PaymentChannelResource\RelationManagers\PaymentMethodsRelationManager;

class PaymentChannelResource extends Resource
{
    use NestedResource;

    protected static ?string $model = PaymentChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3')
                        ->fileAttachmentsDirectory('attachments')
                        ->fileAttachmentsVisibility('private'),
                    Toggle::make('status')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->searchable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('gateway.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentChannels::route('/'),
            'create' => Pages\CreatePaymentChannel::route('/create'),
            'view' => Pages\ViewPaymentChannel::route('/{record}'),
            'edit' => Pages\EditPaymentChannel::route('/{record}/edit'),
            'payment_methods' => Pages\ManageChannelMethod::route('/{record}/payment-methods'),
            'payment_methods.create' => Pages\CreateChannelMethod::route('/{record}/payment-methods/create'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPaymentChannel::class,
            Pages\EditPaymentChannel::class,
            Pages\ManageChannelMethod::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'payment_channels', // Relationship name
            'payment_gateway', // Inverse relationship name
        );
    }
}
