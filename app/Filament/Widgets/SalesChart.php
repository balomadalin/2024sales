<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Collection;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected int | string | array $columnSpan = 6;
    protected static ?string $maxHeight = '338px';
    public ?string $filter = 'y';
    protected static ?string $pollingInterval = null;

    public function getHeading(): string
    {
        return __('incomeAndExpenses');
    }

    protected function getData(): array
    {
        $invoices = Collection::whereNotNull('start_at')
            ->oldest('start_at')
            ->get();
        $expenses = Expense::whereNotNull('expended_at')
            ->oldest('expended_at')
            ->get();

        $period = match($this->filter) {
            'y' => Carbon::parse($invoices[0]->paid_at)->startOfYear()->yearsUntil(now()->addYear()),
            'm' => Carbon::parse($invoices[0]->paid_at)->startOfMonth()->monthsUntil(now()->addMonth()),
        };
        $labels = iterator_to_array($period->map(fn(Carbon $date) => match($this->filter) {
            'y' => $date->format('Y'),
            'm' => $date->isoFormat('YYYY MMM'),
        }));
        array_pop($labels);
        $period = $period->toArray();
        $invoiceData = array_fill(0, count($period)-1, 0);
        $expenseData = array_fill(0, count($period)-1, 0);
        $taxData = array_fill(0, count($period)-1, 0);

        foreach ($period as $i => $date) {
            if ($i == count($period)-1) break;
            foreach ($invoices as $obj) {
                if (CarbonPeriod::create($date, $period[$i+1])->contains($obj->start_at)) {
                    $invoiceData[$i] += $obj->amount_received;
                }
            }
            foreach ($expenses as $obj) {
                if (CarbonPeriod::create($date, $period[$i+1])->contains($obj->expended_at)) {
                    $expenseData[$i] += $obj->net;
                }
            }

        }

        return [
            'datasets' => [
                [
                    'label' => __('income'),
                    'data' => $invoiceData,
                    'fill' => 'start',
                    'backgroundColor' => '#3b82f622',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => trans_choice('expense', 2),
                    'data' => $expenseData,
                    'fill' => 'start',
                    'backgroundColor' => '#f43f5e22',
                    'borderColor' => '#f43f5e',
                ],
            ],
            'labels' => $labels
        ];

    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'y' => __('perYear'),
            'm' => __('perMonth'),
        ];
    }



    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    multiKeyBackground: '#000',
                    callbacks: {
                        label: (context) => ' ' + context.formattedValue + ' RON ' + context.dataset.label,
                        labelColor: (context) => ({
                            borderWidth: 2,
                            borderColor: context.dataset.borderColor,
                            backgroundColor: context.dataset.borderColor + '33',
                        }),
                    },
                },
            },
            hover: {
                mode: 'index',
            },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => value/1 + ' RON',
                    },
                },
            },
            datasets: {
                line: {
                    pointRadius: 0,
                    pointHoverRadius: 0,
                }
            },
            elements: {
                line: {
                    borderWidth: 2,
                    tension: 0.15,
                }
            }
        }
        JS);
    }
}
