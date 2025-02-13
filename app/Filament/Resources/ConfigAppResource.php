<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\ConfigApp;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ConfigAppResource\Pages;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;

class ConfigAppResource extends Resource
{
    protected static ?string $model = ConfigApp::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('tax_fee')
                        ->helperText('Please enter a value in percentage')
                        ->required()
                        ->numeric(),
                    TextInput::make('service_fee')
                        ->helperText('Please enter a value in nominal')
                        ->required()
                        ->numeric(),
                    TextInput::make('call_center')
                        ->helperText('example: 628xxxxxxxx')
                        ->required()
                        ->numeric(),
                    TextInput::make('email_help_center')
                        ->required()
                        ->email(),
                    RichEditor::make('terms_and_conditions'),
                    RichEditor::make('privacy_policy'),
                    TextInput::make('success_redirect_url')
                        ->helperText('example : https://viralmatics.com/success')
                        ->maxLength(255)
                        ->default(null),
                    TextInput::make('failure_redirect_url')
                        ->helperText('example : https://viralmatics.com/failure')
                        ->maxLength(255)
                        ->default(null),
                    Toggle::make('status')
                        ->required(),
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
                TextColumn::make('tax_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('service_fee')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
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
            'index' => Pages\ListConfigApps::route('/'),
            'create' => Pages\CreateConfigApp::route('/create'),
            'view' => Pages\ViewConfigApp::route('/{record}'),
            'edit' => Pages\EditConfigApp::route('/{record}/edit'),
        ];
    }
}
