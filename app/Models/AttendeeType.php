<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Convention used by DynamicFormRenderer's relation_select fields: if a
     * related model defines this, it's used instead of a plain ::query()
     * so the public form only offers appropriate options.
     */
    public static function selectableOptions(): \Illuminate\Support\Collection
    {
        return static::orderBy('order')->get();
    }
}
