<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;


class Collection extends Model
{
    use HasFactory;
    protected $table = 'collections';
    protected $fillable = [
        'clients_id',
        'amount_received',
        'invoice_id',
        'start_at',
        'payment_method',
        'details',
        'total',
    ];


    public function invoices()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, );
    }
    public function invoicess(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }



    public function updateTotal($invoiceId)
    {
        // Obțineți toate facturile asociate colecției
        $invoices = Invoice::where('collection_id', $this->id)->get();

        // Calculați totalul facturilor și actualizați înregistrarea colecției
        $totalValoare = $invoice->sum('total');
        $this->update(['amount_received' => $totalValoare]);
    }


    public function mount()
    {
        // Incarca toate facturile pentru a le afisa in dropdown
        $this->invoices = Invoice::all();
    }

    public function updatedSelectedInvoiceId()
    {
        // Obține valoarea totală a facturii selectate
        $selectedInvoice = Invoice::find($this->selectedInvoiceId);

        if ($selectedInvoice) {
            $this->amountReceived = $selectedInvoice->total;
        }
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'clients_id');
    }

}
