<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    public static function get(string $field) {
        return self::find($field)?->value;
    }

    /**
     * Translated setting label
     */
    public function getLabelAttribute()
    {
        return __($this->field);
    }
}
