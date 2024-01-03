<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Invoice;
use App\Filament\Resources\PositionResource\Widgets\RecentPositionsChart;
use Filament\Resources\Pages\EditRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label(__('download'))
                ->icon('tabler-file-type-pdf')
                ->url(fn (Invoice $record): string => static::$resource::getUrl('download', ['record' => $record]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->icon('tabler-trash'),
        ];
    }
}
