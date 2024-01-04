<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // include logoUrl attribute
    protected $appends = ['url', 'label'];

    protected $hidden = [
        'created_at',
        'logo',
        'updated_at',
    ];


    public static function get(string $field) {
        return self::find($field)?->value;
    }

    /**
     * Translated setting label
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->logo);
    }
    public function getLabelAttribute()
    {
        return __($this->field);
    }
}
