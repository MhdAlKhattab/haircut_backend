<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'branch_name',
    ];

    public function Manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    } 

    public function Deposits(): HasMany
    {
        return $this->hasMany(Cashier_Deposit::class);
    } 

    public function Withdraws(): HasMany
    {
        return $this->hasMany(Cashier_Withdraw::class);
    }

    public function Employees(): HasMany
    {
        return $this->hasMany(Employee::class);
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

    public function Services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function Products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function Salon_Dates(): HasMany
    {
        return $this->hasMany(Salon_Date::class);
    }

    public function General_Service_Providers(): HasMany
    {
        return $this->hasMany(General_Service_Provider::class);
    }

    public function General_Service_Terms(): HasMany
    {
        return $this->hasMany(General_Service_Term::class);
    }

    public function General_Services(): HasMany
    {
        return $this->hasMany(General_Service::class);
    }

    public function Sundry_Products(): HasMany
    {
        return $this->hasMany(Sundry_Product::class);
    }

    public function Suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function Purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
