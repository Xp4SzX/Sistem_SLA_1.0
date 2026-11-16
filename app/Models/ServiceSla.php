<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSla extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'target_uptime' => 'decimal:2',
        'effective_date' => 'date',
        'review_date' => 'date',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}