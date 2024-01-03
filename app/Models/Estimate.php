<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Products;
use App\Models\Client;

class Estimate extends Model
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
    public function clients()
    {
        return $this->belongsTo(Client::class, 'clients_id');
    }

    public function products()
    {
        return $this->hasMany(Products::class , 'estimate_id');


        return $this->belongsTo(Products::class , 'estimate_id');
    }
}
