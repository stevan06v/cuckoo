<?php

namespace App\Filament\Resources;

use App\Models\Customer;
use Carbon\Carbon;
use App\Filament\Resources\TimeResource\Pages;
use App\Filament\Resources\TimeResource\RelationManagers;
use App\Filament\Resources\TimeResource\Widgets\TimesOverview;
use App\Models\Contract;
use App\Models\ContractClassification;
use App\Models\Time;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\IconColumn\IconColumnSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;

class TimeResource extends Resource
{
    protected static ?string $model = Time::class;

    protected static ?string $navigationIcon = 'far-clock';
    protected static ?string $navigationGroup = 'Contracts';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('messages.time.resource.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.time.resource.name');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.time.resource.name_plural');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    DateTimePicker::make('date')
                        ->default(now())
                        ->required()
                        ->label(__('messages.time.form.field_date')),
                    RichEditor::make('description')
                        ->nullable()
                        ->string()
                        ->label(__('messages.time.form.field_description')),
                ])->heading(__('messages.time.form.general'))->collapsible()->collapsed(false),
                Section::make([
                    TextInput::make('total_hours_worked')
                        ->required()
                        ->label(__('messages.time.form.field_total_hours_worked')),
                    TextInput::make('total_minutes_worked')
                        ->label(__('messages.time.form.field_total_minutes_worked')),
                ])->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->heading(__('messages.time.form.time')),
                Section::make([
                    Select::make('contract_classification_id')
                        ->label(__('messages.time.form.field_contract_label'))
                        ->relationship('contractClassification', 'contract.name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn (ContractClassification $record) => $record->contract?->name ?? '')
                ])->columns(1)
                    ->collapsible()
                    ->collapsed(false)
                    ->heading(__('messages.time.form.contract')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->sortable()
                    ->label(__('messages.time.table.date')) // Translated label
                    ->date(),
                TextColumn::make('contractClassification.contract.name')
                    ->label(__('messages.time.table.contract_name'))
                    ->sortable()
                    ->label(__('messages.todo.table.contract'))
                    ->searchable(),
                TextColumn::make('contractClassification.contract.customer.company_name')
                    ->label(__('messages.contract.table.filter_customer'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_hours_worked')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $hours = $record->total_hours_worked;
                        $minutes = $record->total_minutes_worked;
                        return "{$hours}h {$minutes}m";
                    })
                    ->label(__('messages.time.table.total_hours'))
                    ->sortable(),
                IconColumn::make('billed')
                    ->sortable()
                    ->label(__('messages.time.table.billed'))
                    ->boolean()
                    ->visible(Auth::user()->hasRole('Admin'))
                    ->icon(fn(bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->size(IconColumnSize::Medium),
            ])
            ->filters([
                SelectFilter::make('contractClassificationUser')
                    ->relationship('contractClassification.user', 'email')
                    ->label(__('messages.time.filters.contract_classification_user')) // Translated label
                    ->visible(fn() => Auth::user()->hasPermissionTo('View Special Times Filters'))
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('contractClassificationContract')
                    ->relationship(
                        'contractClassification.contract',
                        'name',
                        function (Builder $query) {
                            if (!Auth::user()->hasPermissionTo('View Special Times Filters')) {
                                $query->whereIn(
                                    'contracts.id',
                                    Auth::user()
                                        ->contractClassifications()
                                        ->pluck('contract_id')
                                );
                            }
                        }
                    )
                    ->label(__('messages.time.filters.contract_classification_contract'))
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('contractClassificationContractCustomer')
                    ->relationship(
                        'contractClassification.contract.customer',
                        'company_name'
                    )
                    ->options(function () {
                        $query = Customer::query();

                        if (!Auth::user()->hasPermissionTo('View Special Times Filters')) {
                            $allowedContractIds = Auth::user()
                                ->contractClassifications()
                                ->pluck('contract_id');

                            $query->whereHas('contracts', function ($q) use ($allowedContractIds) {
                                $q->whereIn('contracts.id', $allowedContractIds);
                            });
                        }

                        return $query->pluck('company_name', 'id');
                    })
                    ->label(__('messages.contract.table.filter_customer'))
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('billed')
                    ->label(__('messages.time.filters.billed'))
                    ->visible(Auth::user()->hasPermissionTo('View Special Times Filters'))
                    ->query(fn(Builder $query) => $query->where('billed', true)),
                Filter::make('notBilled')
                    ->label(__('messages.time.filters.not_billed'))
                    ->visible(Auth::user()->hasPermissionTo('View Special Times Filters'))
                    ->query(fn(Builder $query) => $query->where('billed', false))
                    ->default(true),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('messages.time.filters.date_from')), // Translated label
                        DatePicker::make('until')
                            ->label(__('messages.time.filters.date_until')), // Translated label
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make(__('messages.time.filters.date_from') . ' ' . Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make(__('messages.time.filters.date_until') . ' ' . Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('markAsBilled')
                        ->visible(Auth::user()->hasRole('Admin'))
                        ->label(__('messages.time.bulk_actions.mark_as_billed.label'))
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['billed' => true]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->color('success')
                        ->icon('heroicon-o-currency-euro'),
                    BulkAction::make('markAsNotBilled')
                        ->label(__('messages.time.bulk_actions.mark_as_not_billed.label'))
                        ->visible(Auth::user()->hasRole('Admin'))
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['billed' => false]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->color('danger')
                        ->icon('heroicon-o-currency-euro'),

                ]),


            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user && $user->hasPermissionTo('View All Times')) {
            return parent::getEloquentQuery();
        } else {
            return parent::getEloquentQuery()
                ->whereHas('contractClassification', function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimes::route('/'),
            'create' => Pages\CreateTime::route('/create'),
            'view' => Pages\ViewTime::route('/{record}'),
            'edit' => Pages\EditTime::route('/{record}/edit'),
        ];
    }
}
