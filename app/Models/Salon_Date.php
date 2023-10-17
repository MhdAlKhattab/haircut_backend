<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salon_Date extends Model
{
    use HasFactory;

    protected $table = 'salon__dates';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'day',
        'start_time',
        'end_time',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
