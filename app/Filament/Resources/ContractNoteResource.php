<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContractClassification;
use App\Models\ContractNote;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ContractNoteResource extends Resource
{
    protected static ?string $model = ContractNote::class;

    protected static ?string $navigationGroup = 'Contracts';
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    public static function getNavigationGroup(): ?string
    {
        return __('messages.contract_note.resource.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.contract_note.resource.name');
    }

    public static function getPluralLabel(): string
    {
        return __('messages.contract_note.resource.name_plural');
    }


    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'date',
            'contract.name',
            'contract.customer.company_name',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        $contractName = $record->contract->name ?? 'No Contract';
        $customerName = $record->contract->customer->company_name ?? 'No Customer';
        return $record->name . ' - ' . $contractName . ' (' . $customerName . ')';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('messages.contract_note.form.section_general'))->schema([
                    TextInput::make('name')
                        ->label(__('messages.contract_note.form.field_name'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('messages.contract_note.form.field_name')),

                    MarkdownEditor::make('description')
                        ->label(__('messages.contract_note.form.field_description'))
                        ->placeholder(__('messages.contract_note.form.field_description')),

                    DateTimePicker::make('date')
                        ->label(__('messages.contract_note.form.field_date'))
                        ->default(now())
                        ->required(),
                ]),

                Section::make(__('messages.contract_note.form.section_contract'))->schema([

                    Select::make('contract_id')
                        ->label(__('messages.contract_note.form.field_contract'))
                        ->relationship('contract', 'contract.name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn (Contract $record) => $record->name ?? ''),
                    FileUpload::make('attachments')
                        ->label(__('messages.contract_note.form.field_attachments'))
                        ->multiple()
                        ->disk('s3')
                        ->directory('contracts_notes')
                        ->preserveFilenames()
                        ->downloadable()
                        ->acceptedFileTypes(['application/pdf', 'image/*', 'text/plain'])
                        ->maxSize(5120)
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.contract_note.table.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('messages.contract_note.table.date'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract.name')
                    ->label(__('messages.contract_note.table.contract'))
                    ->sortable()
                    ->limit(10)
                    ->searchable(),

                Tables\Columns\TextColumn::make('contract.customer.company_name')
                    ->label(__('messages.contract_note.table.customer'))
                    ->sortable()
                    ->limit(10)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contract_id')
                    ->label(__('messages.contract_note.table.contract'))
                    ->options(function () {
                        $user = Auth::user();
                        return Contract::whereHas('classifications', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })->pluck('name', 'id');
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
        if ($user && $user->hasPermissionTo('View All Notes')) {
            return parent::getEloquentQuery();
        } else {
            return parent::getEloquentQuery()
                ->whereHas('contract.contract_classifications', function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        }
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
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'view' => Pages\ViewNote::route('/{record}'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }
}
