<?php

namespace App\Filament\Widgets;

use App\Enums\ExpenseCategory;
use App\Models\Expense;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components;
use Filament\Notifications\Notification;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Widgets\Widget;

use function Filament\Support\format_number;

class TaxOverview extends Widget implements HasForms, HasInfolists, HasActions
{
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithActions;

    protected int | string | array $columnSpan = 6;
    protected static string $view = 'filament.widgets.tax-overview';
    protected static ?string $maxHeight = '238px';
    protected static int $entryCount = 12;
    public ?string $filter = 'm';

    public function getHeading(): string
    {
        return __('incomeAndExpenses');
    }

    public function taxInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->name('taxOverview')
            ->schema([
                match($this->filter) {
                    'm' => Components\Grid::make(12)->schema($this->getMonthData()),
                    'y' => Components\Grid::make(12)->schema($this->getYearData()),
                }
            ]);
    }

    protected function getFilters(): ?array
    {
        return [
            'm' => __('perMonth'),
            'y' => __('perYear'),
        ];
    }

    private function generateEntries($heading, $labels, $netIncome, $vatExpenses, $totalVat): array
    {
        return [
            Components\TextEntry::make('timeUnit')
                ->label($heading)
                ->columnSpan(3)
                ->fontFamily(FontFamily::Mono)
                ->state($labels)
                ->listWithLineBreaks(),
            Components\TextEntry::make('netIncome')
                ->label(__('income'))
                ->columnSpan(3)
                ->money('ron')
                ->fontFamily(FontFamily::Mono)
                ->state($netIncome)
                ->color(fn (string $state): string => !$state ? 'gray' : 'normal')
                ->listWithLineBreaks()
                ->copyable()
                ->copyableState(fn (string $state): string => format_number((float)$state)),
            Components\TextEntry::make('vatExpenses')
                ->label(__('Expenses'))
                ->columnSpan(3)
                ->money('ron')
                ->fontFamily(FontFamily::Mono)
                ->state($vatExpenses)
                ->color(fn (string $state): string => !$state ? 'gray' : 'normal')
                ->listWithLineBreaks()
                ->copyable()
                ->copyableState(fn (string $state): string => format_number((float)$state)),
            Components\TextEntry::make('totalVat')
                ->label(__('advantage'))
                ->columnSpan(3)
                ->money('ron')
                ->fontFamily(FontFamily::Mono)
                ->state($totalVat)
                ->color(fn (string $state): string => !$state ? 'gray' : 'normal')
                ->listWithLineBreaks()
                ->copyable()
                ->copyableState(fn (string $state): string => format_number((float)$state))



                ->alignment(Alignment::Center)
            ];
    }

    private function getMonthData(): array
    {
        $labels = [];
        $netIncome = [];
        $vatExpenses = [];
        $totalVat = [];

        $dt = Carbon::today();
        for ($i = 0; $i < static::$entryCount; $i++) {
            $labels[] = $dt->locale(app()->getLocale())->monthName;
            $invoices = Invoice::query()
                ->where('start_at', '>=', $dt->startOfMonth()->toDateString())
                ->where('start_at', '<=', $dt->endOfMonth()->toDateString())
                ->get();
            $netEarned = array_sum($invoices->map(fn (Invoice $i) => $i->total)->toArray());
            $vatEarned = array_sum($invoices->map(fn (Invoice $i) => $i->total)->toArray());
            $netIncome[] = $netEarned;

            // Calculate total price expenses for the month
            $expenses = Expense::query()
                ->where('expended_at', '>=', $dt->startOfMonth()->toDateString())
                ->where('expended_at', '<=', $dt->endOfMonth()->toDateString())
                ->get();
            $vatExpended = array_sum($expenses->map(fn (Expense $e) => $e->price)->toArray());
            $vatExpenses[] = $vatExpended;

            // Calculate the difference between total invoice and price expenses
            $totalVat[] = $vatEarned - $vatExpended;

            $dt->subMonthsNoOverflow();
        }

        return $this->generateEntries(__('month'), $labels, $netIncome, $vatExpenses, $totalVat);
    }

    /*private function getQuarterData(): array
    {
        $labels = [];
        $netIncome = [];
        $vatExpenses = [];
        $totalVat = [];

        $dt = Carbon::today();
        for ($i=0; $i < static::$entryCount; $i++) {
            $labels[] = "$dt->year Q$dt->quarter";
            $invoices = Invoice::query()
                ->where('start_at', '>=', $dt->startOfQuarter()->toDateString())
                ->where('start_at', '<=', $dt->endOfQuarter()->toDateString())
                ->get();
            $netEarned = array_sum($invoices->map(fn (Invoice $i) => $i->net)->toArray());
            $vatEarned = array_sum($invoices->map(fn (Invoice $i) => $i->vat)->toArray());
            $netIncome[] = $netEarned;
            $expenses = Expense::query()
                ->where('expended_at', '>=', $dt->startOfQuarter()->toDateString())
                ->where('expended_at', '<=', $dt->endOfQuarter()->toDateString())
                ->where('category', '=', '1')
                ->get();
            $vatExpended = array_sum($expenses->map(fn (Expense $e) => $e->vat)->toArray());
            $vatExpenses[] = $vatExpended;
            $totalVat[] = $vatEarned - $vatExpended;
            $dt->subQuarterNoOverflow();
        }
        return $this->generateEntries(__('quarter'), $labels, $netIncome, $vatExpenses, $totalVat);
    }*/

    private function getYearData(): array
    {
        $labels = [];
        $netIncome = [];
        $vatExpenses = [];
        $totalVat = [];

        $dt = Carbon::today();
        for ($i = 0; $i < static::$entryCount; $i++) {
            $labels[] = $dt->year;

            // Calculate total net and VAT income for the year
            $invoices = Invoice::query()
                ->where('start_at', '>=', $dt->startOfYear()->toDateString())
                ->where('start_at', '<=', $dt->endOfYear()->toDateString())
                ->get();
            $netEarned = array_sum($invoices->map(fn (Invoice $i) => $i->total)->toArray());
            $vatEarned = array_sum($invoices->map(fn (Invoice $i) => $i->total)->toArray());
            $netIncome[] = $netEarned;

            // Calculate total price expenses for the year
            $expenses = Expense::query()
                ->where('expended_at', '>=', $dt->startOfYear()->toDateString())
                ->where('expended_at', '<=', $dt->endOfYear()->toDateString())
                ->get();
            $vatExpended = array_sum($expenses->map(fn (Expense $e) => $e->price)->toArray());
            $vatExpenses[] = $vatExpended;

            // Calculate the difference between total invoice and price expenses
            $totalVat[] = $vatEarned - $vatExpended;

            $dt->subYearNoOverflow();
        }

        return $this->generateEntries(__('year'), $labels, $netIncome, $vatExpenses, $totalVat);
    }

}
