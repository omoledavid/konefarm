<?php

namespace App\Filament\Resources;

use App\Enums\UserStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserOverview;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\DateTimePicker::make('email_verified_at')->visible(fn($livewire) => blank($livewire->record)),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->disabled()
                    ->visible(fn($livewire) => blank($livewire->record))
                    ->maxLength(191),
                Forms\Components\Toggle::make('is_seller')
                    ->required(),
                Forms\Components\Toggle::make('is_buyer')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(191),
                Forms\Components\TextInput::make('alt_phone')
                    ->tel()
                    ->maxLength(191),
                Forms\Components\TextInput::make('address')
                    ->maxLength(191),
                Forms\Components\TextInput::make('state')
                    ->maxLength(191),
                Forms\Components\TextInput::make('city')
                    ->maxLength(191),
                Forms\Components\TextInput::make('country')
                    ->required()
                    ->maxLength(191)
                    ->default('Nigeria'),
                Forms\Components\Textarea::make('bio')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('profile_photo')
                    ->directory('users/profile_photo'),
                Forms\Components\TextInput::make('farm_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('avg_delivery_rating')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('avg_quality_rating')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_reviews')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('alt_phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('farm_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('avg_delivery_rating')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('avg_quality_rating')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_reviews')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')  // Icon for activation
                        ->color('success')  // Color for the button
                        ->action(function ($record) {
                            // Ensure the user is not already active
                            if ($record->status !== UserStatus::ACTIVE) {
                                $record->update(['status' => UserStatus::ACTIVE]);  // Set status to active (1)

                                // Notify the admin or show success message
                                Notification::make()
                                    ->title('User Activated')
                                    ->success()
                                    ->send();
                            }else{
                                // Notify the admin or show success message
                                Notification::make()
                                    ->title('User is Active already')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()  // Ask for confirmation before activation
                        ->modalHeading('Activate User')
                        ->modalSubheading('Are you sure you want to activate this user?'),
                ])->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Set Status to Inactive')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->status = UserStatus::INACTIVE;  // Sets status to 1
                                $record->save();
                            }
                            Notification::make()
                                ->title('Users Deactivated')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()  // Ask for confirmation before executing
                        ->deselectRecordsAfterCompletion()  // Deselect records after bulk action
                        ->icon('heroicon-o-user-minus')  // Choose an appropriate icon
                        ->color('danger'),  // Choose the color for the action button
                    Tables\Actions\BulkAction::make('reactivate')
                        ->label('Set Status to Active')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->status = UserStatus::ACTIVE;  // Sets status to 1
                                $record->save();
                            }
                            Notification::make()
                                ->title('Users Activated')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-check-circle')
                        ->color('success'),
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getWidgets(): array
    {
        return [UserOverview::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
