<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee_Info extends Model
{
    use HasFactory;

    protected $table = 'employee__infos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'current_salary',
        'total_commission',
        'payed_commission',
    ];

    public function Employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
