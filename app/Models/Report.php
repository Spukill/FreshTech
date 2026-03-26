<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id_buyer',
        'description',
        'status'
    ];

    public function buyer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'id_buyer', 'id');
    }

    public function repReview(): HasOne
    {
        return $this->hasOne(ReportReview::class, 'id_report');
    }
}
