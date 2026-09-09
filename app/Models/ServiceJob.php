<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceJob extends Model
{
    protected $fillable = [
        'booking_id', 
        'status', 
        'started_at', 
        'completed_at', 
        'notes',
    ];

    protected function casts():array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime'
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function jobAssignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }
}
