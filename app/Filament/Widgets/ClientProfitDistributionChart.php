<?php

namespace App\Filament\Widgets;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Collection;





class ClientProfitDistributionChart extends ChartWidget
{
    protected int | string | array $columnSpan = 6;
    protected static ?string $maxHeight = '327px';
    public ?string $filter = '';
    protected static ?string $pollingInterval = null;

    public function getHeading(): string
    {
        return __('clientsProfitDistribution');
    }

   /* protected function getData(): array
    {
        $year = $this->filter ? (int) $this->filter : now()->year;
        $from = Carbon::create($year, 1, 31, 12, 0, 0)->startOfYear();
        $to = Carbon::create($year, 1, 31, 12, 0, 0)->endOfYear();
        $invoices = Invoice::whereBetween('start_at', [$from, $to])

            ->get();
        $profit = [];
        foreach ($invoices as $obj) {
            $id = $obj->client->id;
            $profit[$id] = array_key_exists($id, $profit)
                ? $profit[$id] + $obj->net
                : $obj->net;
        }
        $sum = array_sum($profit);
        $labels = array_map(fn ($id, $p) => '(' . round($p/$sum*100, 1) . '%) ' . Client::find($id)->name, array_keys($profit), $profit);
        $colors = array_map(fn ($id) => Client::find($id)->color, array_keys($profit));

        return [
            'datasets' => [
                [
                    'data' => array_values($profit),
                    'borderColor' => $colors,
                    'backgroundColor' => $colors,
                    'hoverOffset' => 4
                ],
            ],
            'labels' => $labels
        ];
    }*/

    protected function getData(): array
{
    $year = $this->filter ? (int) $this->filter : now()->year;
    $from = Carbon::create($year, 1, 31, 12, 0, 0)->startOfYear();
    $to = Carbon::create($year, 1, 31, 12, 0, 0)->endOfYear();

    // Calculul totalului din invoice
    $totalInvoice = Invoice::whereBetween('start_at', [$from, $to])->sum('total');

    // Calculul totalului price din expense
    $totalExpense = Expense::whereBetween('expended_at', [$from, $to])->sum('price');

    // Calculul totalului amount_received din collection
    $totalCollection = Collection::whereBetween('start_at', [$from, $to])->sum('amount_received');

    // Calculul profitului
    $profit = $totalCollection - $totalExpense;

    // Crearea etichetelor și culorilor pentru grafic
    $labels = ['Total Invoice', 'Total Expense', 'Total Collection'];
     // Redenumirea etichetelor
     $labels = [
        __('Total Facturi'),
        __('Total Cheltuieli'),
        __('Total Încasări')
    ];


    // Culori diferite pentru fiecare secțiune
    $colors = ['rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)'];

    // Returnarea datelor sub forma unui array
    return [
        'datasets' => [
            [
                'data' => [$totalInvoice, $totalExpense, $totalCollection],
                'borderColor' => $colors,
                'backgroundColor' => $colors,
                'hoverOffset' => 4
            ],
        ],
        'labels' => $labels,
        'profit' => $profit,
    ];
}



    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getFilters(): ?array
    {
        $res = Invoice::select('start_at')->whereNotNull('start_at')->oldest('start_at')->first();
        if($res) {
            $res = $res->start_at;
            $period = Carbon::parse($res)->startOfYear()->yearsUntil(now());
            $years = array_reverse(iterator_to_array($period->map(fn(Carbon $date) => $date->format('Y'))));
            return array_combine($years, $years);
        }
        return [];
        //Log::debug($res);
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            borderWidth: 0,
            cutout: '60%',
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    multiKeyBackground: '#000',
                    callbacks: {
                        label: (context) => ' ' + context.formattedValue + 'RON' + ' ' + context.label,
                        labelColor: (context) => ({
                            borderWidth: 2,
                            borderColor: context.dataset.borderColor[context.dataIndex],
                            backgroundColor: context.dataset.borderColor[context.dataIndex] + '33',
                        }),
                    },
                },
            },
            scales: {
                y: {
                    display: false,
                },
                x: {
                    display: false,
                }
            },
        }
        JS);
    }
}
