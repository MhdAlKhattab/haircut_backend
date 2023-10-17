<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Canceled_Reservation extends Model
{
    use HasFactory;

    protected $table = 'canceled__reservations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'employee_id',
        'start_date',
        'end_date',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
