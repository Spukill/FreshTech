<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';
    public $timestamps = false;

    protected $fillable = [
        'id_buyer',
        'title',
        'date_not',
        'viewed'
    ];

    protected $casts = [
        'date_not' => 'datetime',
        'viewed' => 'boolean',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'id_buyer');
    }

    // Scope para notificações não lidas
    public function scopeUnread($query)
    {
        return $query->where('viewed', false);
    }

    // Marcar como lida
    public function markAsRead()
    {
        $this->update(['viewed' => true]);
    }
}
