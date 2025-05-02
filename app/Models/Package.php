<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'sessions_count',
        'is_active',
        'discount_percentage',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'discount_percentage' => 'integer',
    ];

    /**
     * Get the services associated with the package.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_service', 'package_id', 'service_id');
    }


    /**
     * Get the specialists associated with the package.
     */
    public function specialists()
    {
        return $this->belongsToMany(Specialist::class, 'package_specialist', 'package_id', 'specialist_id');
    }

    /**
     * Get the bookings for the package.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the final price after discount.
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->price - ($this->price * $this->discount_percentage / 100);
        }
        
        return $this->price;
    }

    /**
     * Scope a query to only include active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
