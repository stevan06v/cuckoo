<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'fas-handshake';
    protected static ?string $navigationGroup = 'Contracts';
    protected static ?int $navigationSort = 1;
    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'full_name', // Customer's full name
            'company_name', // Customer's company name
            'email', // Customer's email
            'phone', // Customer's phone number
            'tax_id', // Customer's tax ID

            // Address fields
            'country',
            'state',
            'city',
            'zip_code',
            'address',
        ];
    }
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->company_name . ' - ' . ($record->full_name ?? 'No Name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('full_name')
                        ->maxLength(255),
                    TextInput::make('company_name')
                        ->maxLength(255)
                        ->required(),
                     TextInput::make('email')
                         ->email(),
                    TextInput::make('phone')
                        ->tel()
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                    TextInput::make('tax_id'),
                ])->heading("General"),
                Section::make('Address')->schema([
                    TextInput::make('country')
                        ->nullable()
                        ->maxLength(255),
                    TextInput::make('state')
                        ->nullable()
                        ->maxLength(255),
                    TextInput::make('city')
                        ->nullable()
                        ->maxLength(255),
                    TextInput::make('zip_code')
                        ->nullable()
                        ->maxLength(255),
                    TextInput::make('address')
                        ->nullable()
                        ->maxLength(255)
                ])->columns(2)
                    ->collapsible(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->limit(30)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                   ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->label('Country')
                    ->options(
                        Customer::query()
                            ->whereNotNull('country')
                            ->distinct()
                            ->pluck('country', 'country')
                            ->toArray()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('state')
                    ->label('State')
                    ->options(
                        Customer::query()
                            ->whereNotNull('state')
                            ->distinct()
                            ->pluck('state', 'state')
                            ->toArray()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('city')
                    ->label('City')
                    ->options(
                        Customer::query()
                            ->whereNotNull('city')
                            ->distinct()
                            ->pluck('city', 'city')
                            ->toArray()
                    )
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if ($user && $user->hasPermissionTo('View All Customers')) {
            return parent::getEloquentQuery();
        } else {
            return parent::getEloquentQuery()
                ->whereHas('contractClassification', function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        }
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ContractsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
