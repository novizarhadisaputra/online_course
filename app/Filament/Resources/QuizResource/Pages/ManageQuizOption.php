<?php

namespace App\Filament\Resources\QuizResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\QuizResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageQuizOption extends ManageRelatedRecords
{
    use NestedPage;
    use NestedRelationManager;

    protected static string $resource = QuizResource::class;

    protected static string $relationship = 'options';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Options';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('option')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('option')
            ->columns([
                Tables\Columns\TextColumn::make('option'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
