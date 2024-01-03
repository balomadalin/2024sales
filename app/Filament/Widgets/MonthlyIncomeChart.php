<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class MonthlyIncomeChart extends ChartWidget
{
    protected int | string | array $columnSpan = 6;
    protected static ?string $maxHeight = '170px';
    public ?string $filter = 'total';
    protected static ?string $pollingInterval = null;

    public function getHeading(): string
    {
        return __('projects');
    }

    protected function getData(): array
    {
        $projects = Project::whereNotNull('start_at')
            ->oldest('due_at')
            ->get();

        if ($projects->isEmpty()) {
            // Handle the case when $projects is empty, for example, by returning an empty array or an error message.
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $period = Carbon::parse($projects[0]->start_at)->startOfYear()->monthsUntil(now()->addMonth());
        $labels = iterator_to_array($period->map(fn(Carbon $date) => $date->format('F Y')));
        $period = $period->toArray();
        $projectData = array_fill(0, count($period) - 1, 0);

        foreach ($period as $i => $date) {
            if ($i == count($period) - 1) break;

            // Suma totală a proiectelor pentru luna curentă
            $totalForMonth = $projects
                ->where('start_at', '>=', $date)
                ->where('start_at', '<', $period[$i + 1])
                ->sum('total');

            $projectData[$i] = match ($this->filter) {
                'total' => $totalForMonth,
            };

            // ... Alte procesări adiționale după cum este necesar
        }

        return [
            'datasets' => [
                [
                    'label' => match ($this->filter) {
                        'total' => __(''),
                    },
                    'data' => $projectData, // Am eliminat array_map și round, pentru a păstra valorile exacte ale contractelor
                    'fill' => 'start',
                    'backgroundColor' => '#3b82f622',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
           // 'total' => __('total'),
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
                        callback: (value) => value / 1 + ' RON',
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
