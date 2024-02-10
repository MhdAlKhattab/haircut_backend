<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'employee_id',
        'customer_id',
        'amount',
        'cash_amount',
        'online_amount',
        'amount_pay_type',
        'discount',
        'amount_after_discount',
        'tax',
        'employee_commission',
        'manager_commission',
        'representative_commission',
        'tip',
        'tip_pay_type',
        'state',
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
        return $this->belongsToMany(Service::class, 'order_service', 'order_id', 'service_id');
    }

    public function Products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')->withPivot('quantity');
    }
}
