<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatus extends Model
{
    protected $table = 'order_status';
    
    public $timestamps = false;

    protected $fillable = [
        'id_notification',
        'id_order',
        'id_buyer'
    ];
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'id_buyer');
    }

    // ! not on the vertical prototype
    /*
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'id_notification');
    }
    */

}
