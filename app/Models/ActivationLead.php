<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationLead extends Model
{
    use HasFactory;

    public const TYPES = ['attendee_interest', 'campus_ambassador'];

    protected $fillable = [
        'activatable_type',
        'activatable_id',
        'type',
        'name',
        'email',
        'phone',
        'message',
    ];

    public function activatable()
    {
        return $this->morphTo();
    }
}
