<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class General_Service extends Model
{
    use HasFactory;

    protected $table = 'general__services';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'provider_id',
        'term_id',
        'amount',
        'tax_state',
    ];

    public function Branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function General_Service_Provider(): BelongsTo
    {
        return $this->belongsTo(General_Service_Provider::class, 'provider_id');
    }

    public function General_Service_Term(): BelongsTo
    {
        return $this->belongsTo(General_Service_Term::class, 'term_id');
    }
}
