<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'first_responded_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * User/Masyarakat pengaju tiket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin penanggung jawab tiket.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Layanan digital terkait.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Log kronologis penanganan tiket.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    /**
     * Lampiran berkas tiket.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }
}