<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Collection;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('tabler-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [

            'all' => Tab::make()
            ->label(__('all'))
              ->badge(Invoice::query()
                ->count()),

            'active' => Tab::make()
            ->label(__('IssuedOnTime'))

            ->badge(Invoice::query()
                ->whereNotNull('due_at')
                ->whereDoesntHave('collections')
                ->whereDate('due_at', '>', now())
                ->count())
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereNotNull('due_at')
                    ->whereDoesntHave('collections')
                    ->whereDate('due_at', '>', now());
            }),



            'waitingForPayment' => Tab::make()
            ->label(__('waitingForPayment'))
            ->badge(Invoice::query()
                ->whereNotNull('due_at')
                ->whereDoesntHave('collections')
                ->whereDate('due_at', '<', now())
                ->count())
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereNotNull('due_at')
                    ->whereDoesntHave('collections')
                    ->whereDate('due_at', '<', now());
            }),


           /* 'finished' => Tab::make()
            ->label(__('Finished'))
            ->badge(Collection::query()
                ->whereNotNull('id')
                ->whereDate('start_at', now())
                ->count())
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereNotNull('id')
                    ->whereDate('start_at', now());
            }),*/





        ];
    }




}
