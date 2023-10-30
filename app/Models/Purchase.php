<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'supplier_id',
        'amount',
        'discount',
        'amount_after_discount',
        'tax',
        'type',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function Products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_product', 'purchase_id', 'product_id')->withPivot('quantity');
    }

    public function Sundry_Products(): BelongsToMany
    {
        return $this->belongsToMany(Sundry_Product::class, 'purchase_sundry', 'purchase_id', 'sundry_id');
    }
}
