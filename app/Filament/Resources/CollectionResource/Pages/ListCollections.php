<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use App\Filament\Resources\CollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Collection;


class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'ALL' => Tab::make()
                ->label(__('All'))
                ->badge(Collection::count()), // Badge pentru toate înregistrările
            'Card' => Tab::make()
                ->label(__('Card'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'card'))
                ->badge(Collection::where('payment_method', 'card')->count()),
            'Cont' => Tab::make()
                ->label(__('Cont'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'cont'))
                ->badge(Collection::where('payment_method', 'cont')->count()),
            'Trezorerie' => Tab::make()
                ->label(__('Trezorerie'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'trezorerie'))
                ->badge(Collection::where('payment_method', 'trezorerie')->count()),
        ];
    }
}
