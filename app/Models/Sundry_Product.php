<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sundry_Product extends Model
{
    use HasFactory;

    protected $table = 'sundry__products';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'name',
        'price',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function Purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_sundry', 'purchase_id', 'sundry_id')->withPivot('quantity');
    }
}
