<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportReview extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id_report',
        'id_review',
    ];

    public function report(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_report', 'id');
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Review::class, 'id_review', 'id');
    }
}
