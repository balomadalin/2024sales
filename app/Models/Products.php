<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;


class Products extends Model
{
    use HasFactory;
    protected $fillable = ['name_product', 'description_product', 'unit', 'tva', 'quantity', 'unit_price', 'discount', 'product_value'];

    public function save(array $options = [])
    {
        $this->product_value = $this->calculateValoareProdus(); // Aici trebuie să ai o metodă de calcul adecvată
        parent::save($options);
    }

    private function calculateValoareProdus()
    {
        // Implementează logica ta de calcul a valorii produsului aici
        $valoare = $this->pret_unitar * $this->cantitate;
        $valoareWithDiscount = $valoare - ($valoare * ($this->discount / 100));
        $valoareWithTvaAndDiscount = $valoareWithDiscount * (1 + $this->tva / 100);

        return $valoareWithTvaAndDiscount;
    }
// În modelul Product
protected static function boot()
{
    parent::boot();

    static::saved(function ($product) {
        // Obține factura asociată produsului și apelează metoda de actualizare
        $factura = $product->factura;
        if ($factura) {
            $factura->updateTotal();
            // Aici poți emite un eveniment Livewire dacă este necesar
        }
    });
}
public function invoice()
{
    return $this->hasMany(Invoice::class, 'id');


    return $this->belongsTo(Invoice::class, 'id');
}
public function sumaTotal()
    {
        return Products::where('invoice_id', $this->id)->sum('product_value');
    }
    public function estimate()
    {
        return $this->hasMany(Estimate::class, 'id');


        return $this->belongsTo(Estimate::class, 'id');
    }
}
