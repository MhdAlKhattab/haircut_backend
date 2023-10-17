<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'name',
        'price',
        'image',
        'duration',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class, 'reservation_service', 'reservation_id', 'service_id');
    }

    public function Orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_service', 'order_id', 'service_id');
    }
}
