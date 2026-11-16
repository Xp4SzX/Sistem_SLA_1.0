<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}