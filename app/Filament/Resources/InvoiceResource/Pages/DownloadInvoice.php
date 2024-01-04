<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Models\Invoice;
use App\Models\Setting;
use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\Page;
use PDF;

class DownloadInvoice extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.template';

    public $record;
    public $settings;

    public function createPDF() {
        // retreive all records from db
        $data = Employee::all();
        // share data to view
        view()->share('employee', $data);

        // download PDF file with download method
        return $pdf->download('pdf_file.pdf');
    }

    public function mount(Invoice $record)
    {
        $this->record = $record;
        $this->settings = Setting::pluck('value', 'field');


        $data = [
            'record' => $record,
            'company' => session('company'),
            'settings' => $this->settings,
        ];
        view()->share('data', $data);
        $pdf = PDF::loadView(self::$view, $data);
        return $pdf->download('pdf_file.pdf');
    }
}
