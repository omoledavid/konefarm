<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\ProductStatus;
use App\Enums\UserStatus;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\UserOverview::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'Active' => Tab::make('Active')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStatus::ACTIVE);
                })
                ->icon('heroicon-o-check-badge'),

            'Inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStatus::INACTIVE);
                })
                ->icon('heroicon-o-clock'),

            'banned' => Tab::make('Banned')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', UserStatus::BANNED);
                })
                ->icon('heroicon-o-trash'),
        ];

    }
}
