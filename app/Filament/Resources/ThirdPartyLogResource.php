<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ThirdPartyLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ThirdPartyLogResource\Pages;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;

class ThirdPartyLogResource extends Resource
{
    protected static ?string $model = ThirdPartyLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('id')
                        ->label('ID')
                        ->required()
                        ->maxLength(36),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('event_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('ip_address')
                        ->maxLength(255)
                        ->default(null),
                    KeyValue::make('data'),
                ]),
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
                TextColumn::make('event_name')
                    ->searchable(),
                TextColumn::make('ip_address')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThirdPartyLogs::route('/'),
            'create' => Pages\CreateThirdPartyLog::route('/create'),
            'view' => Pages\ViewThirdPartyLog::route('/{record}'),
            'edit' => Pages\EditThirdPartyLog::route('/{record}/edit'),
        ];
    }
}
