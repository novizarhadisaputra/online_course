<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                TextInput::make('user_id')
                    ->required()
                    ->maxLength(255)
                    ->placeholder(auth()->user()->name)
                    ->default(auth()->user()->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.id')
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(auth()->user()->hasRole(['super_admin'])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(auth()->user()->hasRole(['super_admin'])),
                Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasRole(['super_admin'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasRole(['super_admin'])),
                ]),
            ]);
    }
}
