<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'gateway_txn_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public const STATUSES = ['initiated', 'pending', 'paid', 'failed', 'refunded'];

    protected $fillable = [
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'gateway',
        'gateway_txn_id',
        'status',
        'payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function payable()
    {
        return $this->morphTo();
    }
}
