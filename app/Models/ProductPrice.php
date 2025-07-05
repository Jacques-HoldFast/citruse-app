<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'year',
        'price_per_kg',
    ];

    protected $casts = [
        'price_per_kg' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the product that owns this price
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get prices for current year
     */
    public function scopeCurrentYear($query)
    {
        return $query->where('year', date('Y'));
    }

    /**
     * Scope to get prices for a specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Get formatted price in Rands
     */
    public function getFormattedPriceAttribute()
    {
        return 'R' . number_format($this->price_per_kg, 2);
    }

    /**
     * Calculate total value for given quantity
     */
    public function calculateTotal($quantityKg)
    {
        return $this->price_per_kg * $quantityKg;
    }

    /**
     * Check if this is the current year's pricing
     */
    public function isCurrentYear()
    {
        return $this->year == date('Y');
    }

    /**
     * Get all available years for pricing
     */
    public static function getAvailableYears()
    {
        return static::distinct('year')
            ->orderBy('year', 'desc')
            ->pluck('year');
    }
}