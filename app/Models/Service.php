<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'duration',
        'image',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the service.
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
    
    /**
     * Get the categories for the service.
     */
    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category', 'service_id', 'category_id')
            ->withTimestamps();
    }

    /**
     * Get the bookings for the service.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the packages that include this service.
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_service', 'service_id', 'package_id')
            ->withTimestamps();
    }

    /**
     * Get the specialists who provide this service.
     */
    public function specialists()
    {
        return $this->belongsToMany(Specialist::class, 'specialist_service', 'service_id', 'specialist_id')
            ->withTimestamps();
    }
}
