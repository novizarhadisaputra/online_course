<?php

namespace App\Filament\Resources;

use App\Models\Ads;
use App\Models\User;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MorphToSelect;
use App\Filament\Resources\AdsResource\Pages;
use Filament\Forms\Components\MorphToSelect\Type;
use App\Models\Event;
use Illuminate\Support\Carbon;

class AdsResource extends Resource
{
    protected static ?string $model = Ads::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    MorphToSelect::make('model')
                        ->types([
                            Type::make(User::class)
                                ->modifyOptionsQueryUsing(fn(Builder $query) => $query
                                    ->where('email_verified_at', '<>', null)
                                    ->whereHas('roles', fn(Builder $role) => $role->where('name', 'instructor')))
                                ->titleAttribute('email'),
                            Type::make(Course::class)
                                ->modifyOptionsQueryUsing(fn(Builder $query) => $query->where('status', true))
                                ->titleAttribute('name'),
                            Type::make(Event::class)
                                ->modifyOptionsQueryUsing(fn(Builder $query) => $query->where('status', true))
                                ->titleAttribute('name'),
                        ])
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),
                    DatePicker::make('start_date')
                        ->native(false)
                        ->helperText(text: 'Timezone Asia/Jakarta')
                        ->timezone('Asia/Jakarta')
                        ->minDate(Carbon::today())
                        ->required(),
                    DatePicker::make('end_date')
                        ->native(false)
                        ->helperText(text: 'Timezone Asia/Jakarta')
                        ->timezone('Asia/Jakarta')
                        ->after('start_time')
                        ->minDate(Carbon::today())
                        ->required(),
                    Toggle::make('status')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('model_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_id'),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAds::route('/create'),
            'edit' => Pages\EditAds::route('/{record}/edit'),
        ];
    }
}
