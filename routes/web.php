<?php

use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;


Route::get('/document/{invoice}', function (Invoice $invoice) {
    $data = [
        'record' => $invoice,
        'company' => session('company'),
    ];

    $pdf = PDF::loadView('filament.resources.invoice-resource.pages.template', $data);

    return $pdf->stream();


    return $pdf->download('pdf_file.pdf');

})->name('document.download');
