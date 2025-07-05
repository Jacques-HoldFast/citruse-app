<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'description',
    ];

    /**
     * Get the prices for this product
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Get the purchase order items for this product
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the current year's price
     */
    public function getCurrentPrice()
    {
        return $this->prices()
            ->where('year', date('Y'))
            ->first();
    }

    /**
     * Get price for a specific year
     */
    public function getPriceForYear($year)
    {
        return $this->prices()
            ->where('year', $year)
            ->first();
    }

    /**
     * Get the current price per kg value
     */
    public function getCurrentPricePerKg()
    {
        $currentPrice = $this->getCurrentPrice();
        return $currentPrice ? $currentPrice->price_per_kg : null;
    }

    /**
     * Scope to get products with current year pricing
     */
    public function scopeWithCurrentPricing($query)
    {
        return $query->with([
            'prices' => function ($q) {
                $q->where('year', date('Y'));
            }
        ]);
    }

    /**
     * Get formatted product info with current price
     */
    public function getProductInfoAttribute()
    {
        return [
            'code' => $this->product_code,
            'description' => $this->description,
            'current_price' => $this->getCurrentPricePerKg(),
        ];
    }
}