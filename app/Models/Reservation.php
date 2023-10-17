<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'employee_id',
        'customer_id',
        'date',
        'total_duration',
        'total_amount',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function Customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function Services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'reservation_service', 'reservation_id', 'service_id');
    }
}
