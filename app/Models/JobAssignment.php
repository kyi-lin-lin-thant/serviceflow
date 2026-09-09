<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAssignment extends Model
{
    protected $fillable = [
        'job_id', 
        'staff_id', 
        'assigned_at', 
        'accepted_at', 
        'unassigned_at',
        'status',
    ];

    protected function casts():array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'unassigned_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(ServiceJob::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
