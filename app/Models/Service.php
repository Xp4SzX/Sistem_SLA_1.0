<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function sla(): HasOne
    {
        return $this->hasOne(ServiceSla::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SlaEvaluation::class);
    }
}