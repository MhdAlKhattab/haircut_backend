<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'name',
        'residence_number',
        'residence_expire_date',
        'health_number',
        'health_expire_date',
        'job',
        'pay_type',
        'salary',
        'income_limit',
        'commission',
        'residence_cost',
        'health_cost',
        'insurance_cost',
        'costs_responsible',
        'state,'
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Info(): HasOne
    {
        return $this->hasOne(Employee_Info::class);
    }

    public function Advance_Pays(): HasMany
    {
        return $this->hasMany(Advance_Pay::class);
    }

    public function Rivals(): HasMany
    {
        return $this->hasMany(Rival::class);
    }

    public function Canceled_Reservations(): HasMany
    {
        return $this->hasMany(Canceled_Reservation::class);
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
