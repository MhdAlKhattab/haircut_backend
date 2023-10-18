<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'name',
        'purchasing_price',
        'selling_price',
        'image',
        'quantity',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product', 'order_id', 'product_id')->withPivot('quantity');
    }

    public function Purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_product', 'purchase_id', 'product_id')->withPivot('quantity');
    }
}
