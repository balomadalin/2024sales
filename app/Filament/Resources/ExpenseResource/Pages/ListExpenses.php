<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Expense; // Asigură-te că ai adăugat această linie pentru a face referire la modelul Expense


class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('tabler-plus'),
        ];
    }



    public function getTabs(): array
    {
        return [
            'ALL' => Tab::make()
                ->label(__('All'))
                ->badge(Expense::count()), // Badge pentru toate înregistrările
            'Card' => Tab::make()
                ->label(__('Card'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'card'))
                ->badge(Expense::where('payment_method', 'card')->count()),
            'Cont' => Tab::make()
                ->label(__('Cont'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'cont'))
                ->badge(Expense::where('payment_method', 'cont')->count()),
            'Trezorerie' => Tab::make()
                ->label(__('Trezorerie'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'trezorerie'))
                ->badge(Expense::where('payment_method', 'trezorerie')->count()),
        ];
    }

}
