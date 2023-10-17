<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rival extends Model
{
    use HasFactory;

    protected $table = 'rivals';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'employee_id',
        'amount',
        'reason',
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
