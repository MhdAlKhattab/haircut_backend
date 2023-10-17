<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashier_Withdraw extends Model
{
    use HasFactory;

    protected $table = 'cashier__withdraws';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'amount',
        'statement',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
