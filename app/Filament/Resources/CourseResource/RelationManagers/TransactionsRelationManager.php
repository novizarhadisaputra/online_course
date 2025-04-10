<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public TransactionStatus $status;

    protected static ?string $title = 'Students';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $this->status))
            ->emptyStateHeading('No students')
            ->recordTitleAttribute('user_id')
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('user.email'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                Action::make('view')
                    ->url(fn(Transaction $record): string => route('filament.admin.resources.users.view', ['record' => $record->user]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
