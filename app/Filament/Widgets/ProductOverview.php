<?php

namespace App\Filament\Widgets;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('All Product', Product::query()->count()),
            Stat::make('Active Product', Product::query()->where('status', ProductStatus::ACTIVE)->count()),
            Stat::make('Pending Product', Product::query()->where('status', ProductStatus::INACTIVE)->count()),
        ];
    }
}
