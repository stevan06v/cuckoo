<?php

namespace App\Filament\Resources\ContractResource\RelationManagers;

use App\Models\ContractClassification;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LoginCredentialsRelationManager extends RelationManager
{
    protected static string $relationship = 'login_credentials';

    public static function getModelLabel(): string
    {
        return __('messages.login_credentials.resource.name');
    }
    public static function getPluralModelLabel(): string
    {
        return __('messages.login_credentials.resource.name');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                ->schema([
                    Section::make(__('messages.login_credentials.form.section_general'))->schema([
                        TextInput::make('name')
                            ->required()
                            ->label(__('messages.login_credentials.form.field_name')),
                        MarkdownEditor::make('description')
                            ->label(__('messages.login_credentials.form.field_description')),
                        TextInput::make('email')
                            ->email()
                            ->label(__('messages.login_credentials.form.field_email')),
                        TextInput::make('password')
                            ->label(__('messages.login_credentials.form.field_password')),
                        FileUpload::make('attachments')
                            ->multiple()
                            ->label(__('messages.login_credentials.form.field_attachments'))
                            ->directory('login_credentials_attachments')
                            ->visibility('public')
                            ->preserveFilenames(),
                    ])->collapsible(),
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
            ->columns([
                TextColumn::make('name')
                    ->label(__('messages.login_credentials.table.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('messages.login_credentials.table.description'))
                    ->limit(50),
            ])
            ->filters([
                // Add filters if needed based on the translations
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
}
