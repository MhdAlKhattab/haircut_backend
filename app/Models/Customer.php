<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'name',
        'phone_number',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function Orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
