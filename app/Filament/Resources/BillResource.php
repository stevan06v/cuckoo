<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\RelationManagers\TimeRelationManagerRelationManager;
use Filament\Forms\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\IconColumn\IconColumnSize;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\Pages\CreateBill;
use App\Filament\Resources\BillResource\Pages\EditBill;
use App\Filament\Resources\BillResource\Pages\ListBills;
use App\Filament\Resources\BillResource\Pages\ViewBill;
use App\Models\Bill;
use App\Models\Contract;
use App\Models\ContractClassification;
use App\Models\Customer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationGroup = 'Contracts';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getModelLabel(): string
    {
        return __('messages.bill.resource.name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.bill.resource.name_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.bill.resource.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.bill.resource.name');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.bill.resource.name_plural');
    }


    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name', // Bill name
            'description',
            'hourly_rate',

            // Attributes from related ContractClassification
            'contractClassification.contract.name',
            'contractClassification.user.name',

            // Attributes from related Customer (via Contract)
            'contractClassification.contract.customer.company_name',
            'contractClassification.contract.customer.email',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $contractName = $record->contractClassification->contract->name ?? 'No Contract';
        return $record->name . ' (' . $contractName . ')';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.bill.form.section_general'))->schema([
                    TextInput::make('name')
                        ->label(__('messages.bill.form.field_name'))
                        ->required()
                        ->maxLength(255),
                    Toggle::make('is_flat_rate')
                        ->label(__('messages.bill.form.field_is_flat_rate'))
                        ->helperText(__('messages.bill.form.field_is_flat_rate_helper'))
                        ->default(false)
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, $set, $state) {
                            if (!$state) {
                                $set('flat_rate_amount', null);
                            }
                        }),

                    TextInput::make('hourly_rate')
                        ->label(__('messages.bill.form.field_hourly_rate'))
                        ->numeric()
                        ->prefix('€')
                        ->disabled(fn(Get $get): bool => $get('is_flat_rate'))
                        ->requiredIf('is_flat_rate', false),

                    TextInput::make('flat_rate_amount')
                        ->label(__('messages.bill.form.field_flat_rate_amount'))
                        ->numeric()
                        ->prefix('€')
                        ->disabled(fn(Get $get): bool => !$get('is_flat_rate'))
                        ->requiredIf('is_flat_rate', true),

                    TextInput::make('flat_rate_hours')
                        ->label(__('messages.bill.form.field_flat_rate_hours'))
                        ->numeric()
                        ->prefix('h')
                        ->disabled(fn(Get $get): bool => !$get('is_flat_rate'))
                        ->requiredIf('is_flat_rate', true),

                    RichEditor::make('description')
                        ->label(__('messages.bill.form.field_description'))
                        ->nullable()
                        ->string(),

                    DatePicker::make('due_to')
                        ->label(__('messages.bill.form.field_due_to')),

                    DatePicker::make('created_on')
                        ->label(__('messages.bill.form.field_created_on'))
                        ->nullable()
                        ->default(now()),

                    Toggle::make('is_payed')
                        ->label(__('messages.bill.form.field_is_payed'))
                        ->nullable()
                ])->collapsible()->collapsed(false),

                Section::make(__('messages.bill.form.section_contract'))->schema([

                    Select::make('contract_classification_id')
                        ->label(__('messages.bill.form.field_contract'))
                        ->relationship('contractClassification', 'contract.name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn (ContractClassification $record) => $record->contract?->name ?? '')
                ])->columns(1)->collapsible()->collapsed(false),

                Section::make(__('messages.bill.form.section_attachments'))->schema([
                    FileUpload::make('attachments')
                        ->label(__('messages.bill.form.field_attachments'))
                        ->columns(1)
                        ->multiple()
                        ->disk('s3')
                        ->nullable()
                        ->directory('bills_attachments')
                        ->downloadable()
                        ->preserveFilenames()
                        ->previewable()
                ])->collapsible()->collapsed(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.bill.resource.name'))
                    ->limit(20)
                    ->sortable()
                    ->searchable(),
               TextColumn::make('contractClassification.user.name')
                    ->label(__('messages.bill.table.user'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contractClassification.contract.name')
                    ->label(__('messages.bill.table.contract'))
                    ->sortable()
                    ->limit(10)
                    ->searchable(),

                TextColumn::make('hourly_rate')
                    ->label(__('messages.bill.table.calculated_total'))
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_flat_rate) {
                            return '/';
                        }
                        $roundedTotalHours = $record
                            ->contractClassification
                            ->times
                            ->map(function ($time) {
                                $hours = $time->total_hours_worked;
                                $minutes = $time->total_minutes_worked;
                                $fractionalHours = $minutes / 60;
                                return $fractionalHours >= 0.5 ? ceil($hours + $fractionalHours) : $hours + $fractionalHours;
                            })
                            ->sum();

                        $calculatedPrice = $state * $roundedTotalHours;
                        return number_format($calculatedPrice, 2) . ' €';
                    }),
                TextColumn::make('flat_rate_amount')
                    ->label(__('messages.bill.form.field_flat_rate_amount'))
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_flat_rate) {
                            return $record->flat_rate_amount ? $record->flat_rate_amount . ' €' : '—';
                        }
                        return '/';
                    }),

            ])
            ->filters([
                Filter::make('user')
                    ->label(__('messages.bill.filters.user.label'))
                    ->form([
                        Select::make('user_id')
                            ->searchable()
                            ->label(__('messages.bill.filters.user.label'))
                            ->options(fn() => User::all()->pluck('name', 'id'))
                            ->placeholder(__('messages.bill.filters.user.placeholder'))
                            ->hidden(fn() => !auth()->user()->hasRole('Admin'))
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['user_id'], function ($query, $userId) {
                            return $query->whereHas('contractClassification.user', function ($q) use ($userId) {
                                $q->where('users.id', $userId);
                            });
                        });
                    }),

                Filter::make('contract')
                    ->label(__('messages.bill.filters.contract.label'))
                    ->form([
                        Select::make('contract_id')
                            ->searchable()
                            ->label(__('messages.bill.filters.contract.label'))
                            ->options(function () {
                                $user = Auth::user();

                                if ($user->hasRole('Admin')) {
                                    return Contract::pluck('name', 'id');
                                }

                                return Contract::whereHas('contract_classifications', function ($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                })->pluck('name', 'id');
                            })
                            ->placeholder(__('messages.bill.filters.contract.placeholder')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['contract_id'], function ($query, $contractId) {
                            return $query->whereHas('contractClassification.contract', function ($q) use ($contractId) {
                                $q->where('contracts.id', $contractId);
                            });
                        });
                    }),
                Filter::make('created_on_range')
                    ->label(__('messages.bill.filters.created_on'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('messages.bill.filters.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('messages.bill.filters.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn($q) => $q->whereDate('created_on', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($q) => $q->whereDate('created_on', '<=', $data['created_until']));
                    }),

                Filter::make('is_payed')
                    ->label(__('messages.bill.filters.payed'))
                    ->query(fn(Builder $query) => $query->where('is_payed', true)),
                Filter::make('is_not_payed')
                    ->label(__('messages.bill.filters.not_payed'))
                    ->query(fn(Builder $query) => $query->where('is_payed', false))->default(true),

                Filter::make('is_flat_rate')
                    ->label(__('messages.bill.filters.flat_rate'))
                    ->query(fn(Builder $query) => $query->where('is_flat_rate', true)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
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

        if ($user && $user->hasPermissionTo('View All Bills')) {
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBills::route('/'),
            'create' => CreateBill::route('/create'),
            'view' => ViewBill::route('/{record}'),
            'edit' => EditBill::route('/{record}/edit')
        ];
    }
}
