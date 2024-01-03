<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Collection;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Colors\Color;

use function Filament\Support\format_money;
use function Filament\Support\format_number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        [$revenue, $monthlyIncome] = $this->getData();
        $previousRevenue = $revenue[count($revenue) - 2];
        $revenueStat = format_money($revenue[count($revenue) - 1], 'RON');
        $revenueDiff = $revenue[count($revenue) - 1] - $previousRevenue;
        $revenueIncrease = $revenueDiff >= 0;
        $revenueDiffPercent = $previousRevenue ? round(abs($revenueDiff) / $previousRevenue * 100) : 0;
        $previousMonthlyIncome = $monthlyIncome[count($monthlyIncome) - 2];
        $monthlyIncomeStat = $monthlyIncome[count($monthlyIncome) - 1];
        $monthlyIncomeDiff = $monthlyIncome[count($monthlyIncome) - 1] - $previousMonthlyIncome;
        $monthlyIncomeIncrease = $monthlyIncomeDiff >= 0;
        $monthlyIncomeDiffPercent = $previousMonthlyIncome ? round(abs($monthlyIncomeDiff) / $previousMonthlyIncome * 100) : 0;

        return [
            Stat::make('monthlyRevenue', $revenueStat)
                ->label(__('Facturări lunare'))
                ->description($revenueDiffPercent . '% ' . ($revenueIncrease ? __('increase') : __('decrease')))
                ->descriptionIcon($revenueIncrease ? 'tabler-trending-up' : 'tabler-trending-down')
                ->chart($revenue)
                ->color($revenueIncrease ? Color::Blue : Color::Red),

            Stat::make('monthlyIncome', format_money($monthlyIncomeStat, 'RON'))
                ->label(__('Venit lunar'))
                ->description($monthlyIncomeDiffPercent . '% ' . ($monthlyIncomeIncrease ? __('increase') : __('decrease')))
                ->descriptionIcon($monthlyIncomeIncrease ? 'tabler-trending-up' : 'tabler-trending-down')
                ->chart($monthlyIncome)
                ->color($monthlyIncomeIncrease ? Color::Blue : Color::Red),

            Stat::make('info', filament()->getBrandName() . ' v' . config('app.version'))
                ->label('Filament ' . \Composer\InstalledVersions::getPrettyVersion('filament/filament'))
                ->description('Laravel ' . \Composer\InstalledVersions::getPrettyVersion('laravel/framework')),
        ];
    }

    protected function getData(): array
    {
        $invoices = Invoice::where('start_at', '>', now()->subWeeks(7)->startOfWeek(Carbon::MONDAY))->get();
        $collections = Collection::where('start_at', '>', now()->subWeeks(7)->startOfWeek(Carbon::MONDAY))->get();

        $period = now()->subWeeks(8)->startOfWeek()->weeksUntil(now()->addWeek()->endOfWeek(Carbon::SUNDAY))->toArray();
        $invoiceRevenue = array_fill(0, count($period) - 1, 0);
        $collectionIncome = array_fill(0, count($period) - 1, 0);

        foreach ($period as $i => $date) {
            if ($i == count($period) - 1) break;

            foreach ($invoices as $invoice) {
                $invoiceStartDate = Carbon::parse($invoice->start_at);
                if ($invoiceStartDate->startOfMonth() <= $period[$i] && $invoiceStartDate->endOfMonth() >= $period[$i + 1]) {
                    $invoiceRevenue[$i] += $invoice->total;
                }
            }

            foreach ($collections as $collection) {
                $collectionStartDate = Carbon::parse($collection->start_at);
                if ($collectionStartDate->startOfMonth() <= $period[$i] && $collectionStartDate->endOfMonth() >= $period[$i + 1]) {
                    // Assuming there is an 'amount_received' attribute in the Collection model
                    $collectionIncome[$i] += $collection->amount_received;
                }
            }
        }

        return [$invoiceRevenue, $collectionIncome];
    }

}
