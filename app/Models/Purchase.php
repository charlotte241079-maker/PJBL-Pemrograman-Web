<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model
{
    protected $fillable = [
        'umkm_id',
        'supplier_id',
        'ingredient_id',
        'quantity',
        'unit_price',
        'total_price',
        'purchase_date',
        'notes',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function expense(): HasOne
    {
        return $this->hasOne(Expense::class);
    }
}
