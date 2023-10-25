<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashier_Deposit extends Model
{
    use HasFactory;

    protected $table = 'cashier__deposits';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'amount',
        'statement',
        'opening_balance',
        'closing_balance',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
