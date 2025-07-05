<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'registered_address',
        'country',
        'vat_number',
        'primary_sales_contact_name',
        'primary_sales_contact_email',
        'primary_sales_contact_telephone',
        'primary_logistics_contact_name',
        'primary_logistics_contact_email',
        'primary_logistics_contact_telephone',
    ];

    /**
     * Get the purchase orders for this supplier
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the full contact information formatted
     */
    public function getPrimarySalesContactAttribute()
    {
        return [
            'name' => $this->primary_sales_contact_name,
            'email' => $this->primary_sales_contact_email,
            'telephone' => $this->primary_sales_contact_telephone,
        ];
    }

    /**
     * Get the full logistics contact information formatted
     */
    public function getPrimaryLogisticsContactAttribute()
    {
        return [
            'name' => $this->primary_logistics_contact_name,
            'email' => $this->primary_logistics_contact_email,
            'telephone' => $this->primary_logistics_contact_telephone,
        ];
    }
}