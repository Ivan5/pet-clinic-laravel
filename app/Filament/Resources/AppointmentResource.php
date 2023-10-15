<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\Pages\EditAppointment;
use App\Models\Appointment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    DatePicker::make('date')->required()->native(false),
                    TimePicker::make('start')->required()->seconds(false)->displayFormat('h:i A')->native(false)->minutesStep(10),
                    TimePicker::make('end')->required()->seconds(false)->displayFormat('h:i A')->native(false)->minutesStep(10),
                    Select::make('pet_id')->relationship('pet', 'name')->required()->searchable()->preload(),
                    Select::make('status')->options(AppointmentStatus::class)->visibleOn(EditAppointment::class)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pet.name')->searchable()->sortable(),
                TextColumn::make('date')->date('M d Y')->searchable()->sortable(),
                TextColumn::make('start_time')->time('h:i A')->label('From')->sortable(),
                TextColumn::make('end_time')->time('h:i A')->label('To')->sortable(),
                TextColumn::make('status')->badge()->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('confirm')->action(function (Appointment $record) {
                    $record->status = AppointmentStatus::CONFIRMED;
                    $record->save();
                })->visible(fn (Appointment $record) => $record->status === AppointmentStatus::CREATED)->color('success')->icon('heroicon-o-check'),
                Action::make('cancel')->action(function (Appointment $record) {
                    $record->status = AppointmentStatus::CANCELED;
                    $record->save();
                })->visible(fn (Appointment $record) => $record->status !== AppointmentStatus::CANCELED)->color('danger')->icon('heroicon-o-x-mark'),
                EditAction::make()
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
