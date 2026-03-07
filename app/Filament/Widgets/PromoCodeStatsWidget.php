<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\PromoCode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PromoCodeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalPromos = PromoCode::count();
        $totalUses = PromoCode::sum('times_used');
        $ordersWithPromo = Order::whereNotNull('promo_code_id')->count();

        return [
            Stat::make('Промокодов', $totalPromos)
                ->description('всего в системе'),
            Stat::make('Использований', number_format($totalUses))
                ->description('сумма по всем промокодам'),
            Stat::make('Заявок с промокодом', $ordersWithPromo)
                ->description('всего заявок'),
        ];
    }
}
