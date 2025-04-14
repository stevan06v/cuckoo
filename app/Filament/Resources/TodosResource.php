<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TodosResource\Pages;
use App\Filament\Resources\TodosResource\RelationManagers;
use App\Models\Contract;
use App\Models\ContractClassification;
use App\Models\Todo;
use App\Models\Todos;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TodosResource extends Resource
{
    protected static ?string $model = Todo::class;

    protected static ?string $navigationGroup = 'Contracts';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('messages.todo.resource.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.todo.resource.name');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.todo.resource.name_plural');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.todo.form.section_general'))->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label(__('messages.todo.form.field_name')),
                    DateTimePicker::make('due_to')
                        ->required()
                        ->label(__('messages.todo.form.field_due_to')),
                    MarkdownEditor::make('description')
                        ->label(__('messages.todo.form.field_description')),
                    Toggle::make('is_done')
                        ->label(__('messages.todo.form.field_is_done'))
                        ->default(false),
                    Select::make('priority')
                        ->options([
                            'low' => __('messages.todo.form.field_priority.low'),
                            'mid' => __('messages.todo.form.field_priority.medium'),
                            'high' => __('messages.todo.form.field_priority.high'),
                        ])
                        ->default('mid')
                        ->required()
                        ->label(__('messages.todo.form.field_priority_label')),
                    FileUpload::make('attachments')
                        ->multiple()
                        ->downloadable()
                        ->previewable()
                        ->preserveFilenames()
                        ->disk('s3')
                        ->directory('todos_attachments')
                        ->label(__('messages.todo.form.field_attachments'))
                ])->heading(__('messages.todo.form.section_general')),

                Section::make(__('messages.todo.form.section_contract'))->schema([
                    Select::make('contract_id')
                        ->label(__('messages.todo.form.field_contract_classification')) // Du kannst ggf. eine neue Übersetzung hinzufügen
                        ->relationship('contract', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('messages.todo.table.name')),

                // Ändere hier 'contract_classification.contract.name' zu 'contract.name'
                TextColumn::make('contract.name')
                    ->label(__('messages.todo.table.contract'))
                    ->sortable(),

                // Ändere hier 'contract_classification.contract.customer.company_name' zu 'contract.customer.company_name'
                TextColumn::make('contract.customer.company_name')
                    ->label(__('messages.todo.table.customer'))
                    ->sortable(),

                TextColumn::make('due_to')
                    ->time()
                    ->sortable()
                    ->label(__('messages.todo.table.due_to')),

                TextColumn::make('priority')
                    ->sortable()
                    ->label(__('messages.todo.table.priority')),

                ToggleColumn::make('is_done')
                    ->label(__('messages.todo.table.is_done'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('contract')
                    ->label(__('messages.call.form.section_contract'))
                    ->options(function () {
                        $userId = Auth::id();
                        return Contract::whereHas('contract_classifications', function (Builder $query) use ($userId) {
                            $query->where('user_id', $userId);
                        })->pluck('name', 'id');
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['contract'])) {
                            return $query->where('contract_id', $data['contract']);
                        }
                        return $query;
                    }),
                SelectFilter::make('priority')
                    ->label(__('messages.todo.table.priority'))
                    ->options([
                        'low' => __('messages.todo.form.field_priority.low'),
                        'mid' => __('messages.todo.form.field_priority.medium'),
                        'high' => __('messages.todo.form.field_priority.high'),
                    ]),

                Filter::make('is_done')
                    ->label(__('messages.todo.table.is_done'))
                    ->query(fn (Builder $query) => $query->where('is_done', true))
                    ->toggle(),

                Filter::make('due_to')
                    ->label(__('messages.todo.table.due_to'))
                    ->form([
                        DatePicker::make('due_from')->label(__('messages.todo.form.field_due_to')),
                        DatePicker::make('due_until')->label(__('messages.todo.form.field_due_to'))
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['due_from'], fn ($query, $date) => $query->whereDate('due_to', '>=', $date))
                            ->when($data['due_until'], fn ($query, $date) => $query->whereDate('due_to', '<=', $date));
                    }),


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


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user && $user->hasPermissionTo('View All Todos')) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()
            ->whereHas('contract.contract_classifications', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTodos::route('/'),
            'create' => Pages\CreateTodos::route('/create'),
            'view' => Pages\ViewTodos::route('/{record}'),
            'edit' => Pages\EditTodos::route('/{record}/edit'),
        ];
    }
}
