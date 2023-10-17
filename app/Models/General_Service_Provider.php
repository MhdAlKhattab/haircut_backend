<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class General_Service_Provider extends Model
{
    use HasFactory;

    protected $table = 'general__service__providers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'name',
        'tax_state',
        'tax_number',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function General_Services(): HasMany
    {
        return $this->hasMany(General_Service::class);
    }
}
