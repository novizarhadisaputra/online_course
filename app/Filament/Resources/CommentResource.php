<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\CommentResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\CommentResource\Pages\CreateReplyComment;
use App\Filament\Resources\CommentResource\Pages\ManageReplyComment;

class CommentResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('description')
                        ->required()
                        ->maxLength(255),
                    Hidden::make('user_id')->default(auth()->id())
                ])
            ]);
    }

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->description;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable(),
                ToggleColumn::make('status'),
                TextColumn::make('user.name'),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            // 'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
            'comments' => ManageReplyComment::route('/{record}/comments'),
            'comments.create' => CreateReplyComment::route('/{record}/comments/create'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditComment::class,
            Pages\ManageReplyComment::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'comments', // Relationship name
            'model', // Inverse relationship name
        );
    }
}
