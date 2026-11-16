<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaEvaluation extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'evaluation_date' => 'date',
        'follow_up_progress' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}