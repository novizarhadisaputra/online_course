<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
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
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentChannelResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\PaymentChannelResource\RelationManagers;
use App\Filament\Resources\PaymentChannelResource\RelationManagers\MethodsRelationManager;

class PaymentChannelResource extends Resource
{
    use NestedResource;

    protected static ?string $model = PaymentChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->name;
    }

    // public static function getBreadcrumbs(Model $record, string $operation): array
    // {

    //     return [
    //         "" => $record->name,
    //     ];
    // }

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
                    Textarea::make('description')
                        ->columnSpanFull(),
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
                TextColumn::make('payment_gateway_id')
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
        return [
            MethodsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentChannels::route('/'),
            'create' => Pages\CreatePaymentChannel::route('/create'),
            'view' => Pages\ViewPaymentChannel::route('/{record}'),
            'edit' => Pages\EditPaymentChannel::route('/{record}/edit'),
            'methods' => Pages\ManageChannelMethod::route('/{record}/methods'),
            'methods.create' => Pages\CreateChannelMethod::route('/{record}/methods/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'channels', // Relationship name
            'gateway', // Inverse relationship name
        );
    }
}
