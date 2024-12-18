<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractLoginCredentialsResource\Pages;
use App\Filament\Resources\ContractLoginCredentialsResource\RelationManagers;
use App\Models\ContractLoginCredentials;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractLoginCredentialsResource extends Resource
{
    protected static ?string $model = ContractLoginCredentials::class;

    protected static ?string $navigationGroup = 'Contracts';
    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'fas-database';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractLoginCredentials::route('/'),
            'create' => Pages\CreateContractLoginCredentials::route('/create'),
            'view' => Pages\ViewContractLoginCredentials::route('/{record}'),
            'edit' => Pages\EditContractLoginCredentials::route('/{record}/edit'),
        ];
    }
}
