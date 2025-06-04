<?php

namespace App\Filament\Resources\PaymentGatewayResource\RelationManagers;

use App\Filament\Resources\PaymentChannelResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class PaymentChannelsRelationManager extends RelationManager
{
    use NestedRelationManager;

    protected static string $relationship = 'payment_channels';

    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define the it
    public static string $nestedResource = PaymentChannelResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
