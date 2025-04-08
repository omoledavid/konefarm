<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('All Users', User::query()->count()),
            Stat::make('Active Users', User::query()->where('status', UserStatus::ACTIVE)->count()),
            Stat::make('Inactive User', User::query()->where('status', UserStatus::INACTIVE)->count()),
        ];
    }
}
