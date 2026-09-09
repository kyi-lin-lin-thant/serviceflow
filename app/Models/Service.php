<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 
        'description', 
        'category_id', 
        'price', 
        'duration_minutes', 
        'status',
    ];

    protected function casts():array
    {
        return [
            'price' => 'decimal:2',
            'duration_minutes' => 'integer'
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
