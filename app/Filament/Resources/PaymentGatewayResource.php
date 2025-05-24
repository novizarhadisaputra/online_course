<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentGateway;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\PaymentGatewayResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\PaymentGatewayResource\RelationManagers\PaymentChannelsRelationManager;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;

class PaymentGatewayResource extends Resource
{
    use NestedResource;

    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Master Data';

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->name;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('images')
                        ->visibility('private')->disk('s3')
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    KeyValue::make('configs')
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
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('images')
                    ->visibility('private')->disk('s3'),
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('payment_channels_count')->counts([
                    'payment_channels' => fn(Builder $query) => $query->where('payment_channels.status', true),
                ])->formatStateUsing(fn(string $state): string => "$state channels"),
                TextColumn::make('payment_methods_count')->counts([
                    'payment_methods' => fn(Builder $query) => $query->where('payment_methods.status', true),
                ])->formatStateUsing(fn(string $state): string => "$state methods"),
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
            PaymentChannelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentGateways::route('/'),
            'create' => Pages\CreatePaymentGateway::route('/create'),
            'view' => Pages\ViewPaymentGateway::route('/{record}'),
            'edit' => Pages\EditPaymentGateway::route('/{record}/edit'),
            'payment_channels' => Pages\ManageGatewayChannels::route('/{record}/payment-channels'),
            'payment_channels.create' => Pages\CreateGatewayChannel::route('/{record}/payment-channels/create'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
