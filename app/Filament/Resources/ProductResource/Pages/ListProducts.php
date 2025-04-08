<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductStatus;
use App\Filament\Resources\ProductResource;
use App\Filament\Widgets\ProductOverview;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            ProductOverview::class,
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'published' => Tab::make('Approved')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', ProductStatus::ACTIVE);
                })
                ->icon('heroicon-o-check-badge'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', ProductStatus::INACTIVE);
                })
                ->icon('heroicon-o-clock'),

            'deleted' => Tab::make('Deleted')
                ->modifyQueryUsing(function ($query) {
                    $query->onlyTrashed();
                })
                ->icon('heroicon-o-trash'),
        ];

    }
}
