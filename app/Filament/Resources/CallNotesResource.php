<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallNotesResource\Pages;
use App\Filament\Resources\CallNotesResource\RelationManagers;
use App\Models\CallNote;
use App\Models\CallNotes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CallNotesResource extends Resource
{
    protected static ?string $model = CallNote::class;

    protected static ?int $navigationSort= 2;
    protected static ?string $navigationGroup = 'Calls';

    protected static ?string $navigationIcon = 'fas-book';

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
            'index' => Pages\ListCallNotes::route('/'),
            'create' => Pages\CreateCallNotes::route('/create'),
            'view' => Pages\ViewCallNotes::route('/{record}'),
            'edit' => Pages\EditCallNotes::route('/{record}/edit'),
        ];
    }
}
