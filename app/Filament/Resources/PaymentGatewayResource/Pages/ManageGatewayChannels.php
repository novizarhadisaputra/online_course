<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\PaymentGatewayResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageGatewayChannels extends ManageRelatedRecords
{
    use NestedPage; // Since this is a standalone page, we also need this trait
    use NestedRelationManager;

    protected static string $resource = PaymentGatewayResource::class;

    protected static string $relationship = 'payment_channels';

    public function getTitle(): string {
        return $this->record->name . "'s " . ' channels';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('payment_methods_count')->default(0),
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
