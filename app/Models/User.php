<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Role constants
    const ROLE_SYSTEM_ADMIN = 'system_admin';
    const ROLE_PURCHASING_MANAGER = 'purchasing_manager';
    const ROLE_FIELD_SALES_ASSOCIATE = 'field_sales_associate';

    /**
     * Check if user is system administrator
     */
    public function isSystemAdmin(): bool
    {
        return $this->role === self::ROLE_SYSTEM_ADMIN;
    }

    /**
     * Check if user is purchasing manager
     */
    public function isPurchasingManager(): bool
    {
        return $this->role === self::ROLE_PURCHASING_MANAGER;
    }

    /**
     * Check if user is field sales associate
     */
    public function isFieldSalesAssociate(): bool
    {
        return $this->role === self::ROLE_FIELD_SALES_ASSOCIATE;
    }

    /**
     * Check if user can manage suppliers/distributors
     */
    public function canManageSuppliers(): bool
    {
        return $this->isSystemAdmin() || $this->isPurchasingManager();
    }

    /**
     * Check if user can create purchase orders
     */
    public function canCreatePurchaseOrders(): bool
    {
        return $this->isSystemAdmin() || $this->isPurchasingManager();
    }

    /**
     * Check if user can update purchase order status
     */
    public function canUpdatePurchaseOrderStatus(): bool
    {
        return $this->isSystemAdmin() || $this->isFieldSalesAssociate();
    }

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_SYSTEM_ADMIN => 'System Administrator',
            self::ROLE_PURCHASING_MANAGER => 'Purchasing Manager',
            self::ROLE_FIELD_SALES_ASSOCIATE => 'Field Sales Associate',
        ];
    }

    /**
     * Get human readable role name
     */
    public function getRoleDisplayName(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }
}