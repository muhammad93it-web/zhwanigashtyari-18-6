<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Available permission modules (key => Kurdish label).
     */
    public const MODULES = [
        'finance'        => 'دارایی (وەرگرتن، خەرجی، قەرز)',
        'trading'        => 'کڕین و فرۆشتن و کۆگا',
        'contractors'    => 'وەستا و بەڵێندەرایەتی',
        'reports'        => 'ڕاپۆرتەکان',
        'documents'      => 'نووسراوەکان',
        'print_center'   => 'چاپکردنی بەشەکان',
        'clients'        => 'کڕیاران و کەسەکان',
        'transactions'   => 'مامەڵە گشتییەکان',
        'exchange_rates' => 'ڕێژەی گۆڕینی دراو',
    ];

    protected $fillable = ['name', 'email', 'password', 'is_admin', 'permissions'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_admin'          => 'boolean',
        'permissions'       => 'array',
    ];

    /**
     * Admins can access everything; others only their granted modules.
     */
    public function hasAccess(string $module): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return in_array($module, $this->permissions ?? [], true);
    }
}
