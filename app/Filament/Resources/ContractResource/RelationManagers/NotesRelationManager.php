<?php

namespace App\Filament\Resources\ContractResource\RelationManagers;

use App\Models\ContractClassification;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('General Information')->schema([
                    TextInput::make('name')
                        ->label('Title of the note')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Set a title for the note'),

                    MarkdownEditor::make('description')
                        ->label('Description')
                        ->placeholder('Describe the note'),

                    DateTimePicker::make('date')
                        ->label('Date')
                        ->default(now())
                        ->required(),
                ]),
                Section::make('More Information')->schema([
                    FileUpload::make('attachments')
                        ->label('Attachments')
                        ->multiple()
                        ->directory('contracts_notes')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['application/pdf', 'image/*', 'text/plain'])
                        ->maxSize(5120)
                        ->hint('Acceted formats: PDF oder Bilder.'),
                ]),
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->sortable(),
                TextColumn::make('contractClassification.contract.name')
                    ->label('Contract')
                    ->sortable()
                    ->limit(10)
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $user = Auth::user();
                    $contractId = $this->ownerRecord->id;

                    $contractClassification = ContractClassification::where('user_id', $user->id)
                        ->where('contract_id', $contractId)
                        ->first();

                    if ($contractClassification) {
                        $data['contract_classification_id'] = $contractClassification->id;
                    }
                    return $data;
                })
            ])
            ->actions([
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
            return self::getEloquentQuery();
        } else {
            return self::getEloquentQuery()
                ->whereHas('classifications', function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        }
    }
}
