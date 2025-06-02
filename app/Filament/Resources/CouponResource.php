<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Coupon;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Enums\CouponType;
use Filament\Tables\Table;
use App\Enums\DiscountType;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\CouponResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\CouponResource\RelationManagers\UsersRelationManager;
use App\Filament\Resources\CouponResource\RelationManagers\CoursesRelationManager;
use App\Filament\Resources\CouponResource\RelationManagers\CategoriesRelationManager;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('images')
                        ->visibility('private')
                        ->disk('s3')
                        ->image()
                        ->previewable()
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3')
                        ->fileAttachmentsDirectory('attachments')
                        ->fileAttachmentsVisibility('private'),
                    TextInput::make('code')
                        ->maxLength(255)
                        ->visibleOn('edit')
                        ->default(null),
                    Select::make('type')
                        ->options(CouponType::class)
                        ->live()
                        ->required(),
                    Select::make('discount_type')
                        ->options(DiscountType::class)
                        ->required()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            if ($state == DiscountType::PERCENT->value) {
                                $set('max_amount', 0);
                                if ($get('discount_value') > 100) {
                                    $set('discount_value', 100);
                                }
                            } else {
                                $set('max_amount', $get('discount_value'));
                            }
                        }),
                    TextInput::make('discount_value')
                        ->prefixIcon(fn(Get $get): string => $get('discount_type') == DiscountType::FIXED->value ? 'heroicon-o-currency-dollar' : 'heroicon-o-percent-badge')
                        ->required()
                        ->live(debounce: 500)
                        ->numeric()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $get('discount_type') === DiscountType::FIXED->value && $set('max_amount', $state);
                            if ($get('discount_type') === DiscountType::PERCENT->value && $state > 100) {
                                $set('discount_value', 100);
                            }
                        }),
                    TextInput::make('max_amount')
                        ->numeric()
                        ->default(null),
                    TextInput::make('minimum_order')
                        ->numeric()
                        ->default(null),
                    TextInput::make('max_usable_times')
                        ->required()
                        ->numeric()
                        ->default(1),
                    TextInput::make('user_limit_usage')
                        ->required()
                        ->numeric()
                        ->default(1),
                    DateTimePicker::make('expired_at')->default(now()->addDay()),
                    Toggle::make('status')
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('images')
                    ->visibility('private')
                    ->disk('s3'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->searchable(),
                TextColumn::make('type'),
                TextColumn::make('discount_type'),
                TextColumn::make('discount_value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('code')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('max_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('minimum_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_usable_times')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
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
            CoursesRelationManager::make(),
            CategoriesRelationManager::make(),
            UsersRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'view' => Pages\ViewCoupon::route('/{record}'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
