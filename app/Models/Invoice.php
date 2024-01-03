<?php

namespace App\Models;

use App\Enums\PricingUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Products;
use App\Models\Client;



use function Filament\Support\format_money;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['clients_id','series',
    'number',
    'start_at',
     'numar',
     'due_at',
     'total',
     'products_id',
     'product_value'

];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pricing_unit' => PricingUnit::class,
    ];

    /**
     * Get the client that ordered the project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


    public function client()
    {
        return $this->belongsTo(Client::class, 'clients_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, );
    }


    /**
     * The positions of this project.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Number of hours worked for this invoice
     */
    public function getHoursAttribute()
    {
        $hours = 0;
        foreach ($this->positions as $position) {
            $hours += $position->duration;
        }
        return $hours;
    }

    /**
     * Number of hours worked for this invoice formatted
     */
    public function getHoursFormattedAttribute()
    {
        return $this->hours . ' ' . trans_choice('hour', $this->hours);
    }

    /**
     * Net amount of all assigned positions
     */
    /*public function getNetAttribute()
    {
        $net = 0;
        if ($this->pricing_unit === PricingUnit::Project) {
            $net = $this->price;
        } else {
            $net += $this->hours * $this->price / match ($this->pricing_unit) {
                PricingUnit::Hour => 1,
                PricingUnit::Day => 8,
            };
        }
        return round($net, 2) - $this->discount;
    }*/

    /**
     * Net amount of all assigned positions formatted
     */
    public function getNetFormattedAttribute()
    {
        return format_money($this->net, 'eur');
    }

    /**
     * Vat amount of current net amount
     */
    public function getVatAttribute()
    {
        return round($this->net * $this->vat_rate, 2);
    }

    /**
     * Gross amount of all assigned positions
     */
    public function getGrossAttribute()
    {
        return $this->taxable
            ? $this->net + $this->vat
            : $this->net;
    }

    /**
     * Final total amount of invoice
     */
    public function getFinalAttribute()
    {
        return $this->gross - $this->deduction;
    }

    /**
     * Calculate the current invoice number of format YYYYMMDD##ID
     */
    public function getCurrentNumberAttribute()
    {
        return now()->format('Ymd') . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function afiseazaIncasarilePentruFactura($factura_id)
{
    $factura = Factura::with('incasari')->find($factura_id);

    return view('pagina_cu_incasari', compact('factura'));
}
public function clients()
    {
        return $this->belongsTo(Client::class, 'clients_id');
    }
    public function incasari()
    {
        return $this->hasMany(Incasari::class, 'incasaris_id');
    }

    public function updateTotalFromProducts()
    {
        // Get the sum of 'total' values from related products
        $totalFromProducts = $this->products()->sum('total');

        // Update the 'total' field in the 'facturis' table
        $this->update(['total' => $totalFromProducts]);
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'invoice_id', 'id');


        return $this->belongsTo(Products::class, 'invoice_id', 'id');
    }


    public function collections()
    {
        return $this->hasMany(Collection::class,'invoice_id' );
    }

// În modelul Facturi
public function updateTotal()
{
    // Calculează suma totală a valorilor 'valoare_produs' pentru produsele asociate acelei facturi
    $totalValoare = $this->products->sum('product_value');

    // Actualizează câmpul 'total' în tabela 'facturis'
    $this->update(['total' => $totalValoare]);
}

/*public function store(Request $request)
{
    $data = $request->validate([
        // ... validarea altor câmpuri ale facturii
        'clients_id' => 'required|exists:clients,id',
    ]);

    // Creează o nouă factură și asociază clientul
    $invoice = Invoice::create([
        // ... atributele facturii
        'clients_id' => $data['clients_id'],
    ]);

    // Restul logicii de salvare a produselor, etc.

    return redirect()->route('invoices.index');
}*/


}
