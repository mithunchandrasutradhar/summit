<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormDefinition extends Model
{
    use HasFactory;

    public const KEYS = [
        'summit_registration',
        'award_nomination',
        'sponsorship_enquiry',
        'exhibitor_application',
        'forum_membership',
    ];

    protected $fillable = [
        'key',
        'name',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}
