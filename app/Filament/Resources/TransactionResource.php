<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Services\TransactionService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\TransactionResource\Pages;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = '';

    protected static ?string $navigationGroup = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('code')
                        ->maxLength(255)
                        ->default(null),
                    TextInput::make('payment_method')
                        ->maxLength(255)
                        ->default(null),
                    TextInput::make('payment_channel')
                        ->maxLength(255)
                        ->default(null),
                    TextInput::make('total_qty')
                        ->required()
                        ->numeric()
                        ->default(1),
                    TextInput::make('total_price')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required(),
                    TextInput::make('status')
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('payment_method.name')
                    ->searchable(),
                TextColumn::make('total_qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('status'),
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
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->visible(auth()->user()->can('update_transaction')),
                    MediaAction::make('proof')
                        ->icon(icon: 'heroicon-o-document-text')
                        ->media(fn($record) => $record->hasMedia('proofs') ? $record->getMedia('proofs')->first()->getFullUrl() : null)
                        ->visible(fn($record) => $record->hasMedia('proofs')),
                    Action::make('confirmation payment')
                        ->icon(icon: 'heroicon-o-check-circle')
                        ->action(function (Transaction $record) {
                            $record->status = 'success';
                            $record->save();
                            TransactionService::enrollmentProcess($record);
                        })
                        ->requiresConfirmation()
                        ->visible(fn(Transaction $record) => $record->status == 'pending' && $record->hasMedia('proofs'))
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
