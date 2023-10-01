<?php

namespace App\Filament\Resources;

use App\Enums\PetType;
use App\Filament\Resources\PetResource\Pages;
use App\Models\Pet;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    FileUpload::make('avatar')->image()->imageEditor(),
                    TextInput::make('name')->required(),
                    DatePicker::make('date_of_birth')
                        ->native(false)
                        ->displayFormat('M d Y')
                        ->required(),
                    Select::make('type')->native(false)->options(PetType::class),
                    Select::make('owner_id')
                        ->nullable()
                        ->relationship('owner', 'name')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Section::make([
                                TextInput::make('name')->required(),
                                TextInput::make('email')->email()->required(),
                                TextInput::make('phone')->tel()->required(),
                            ]),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->circular(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('type')->sortable()->searchable(),
                TextColumn::make('date_of_birth')->date('M d Y')->sortable(),
                TextColumn::make('owner.name')->sortable()->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListPets::route('/'),
            'create' => Pages\CreatePet::route('/create'),
            'edit' => Pages\EditPet::route('/{record}/edit'),
        ];
    }
}
