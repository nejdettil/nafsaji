<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'specialty',
        'bio',
        'experience_years',
        'education',
        'certifications',
        'hourly_rate',
        'is_verified',
        'is_featured',
        'availability',
        'rating'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'experience_years' => 'integer',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'decimal:1',
        'availability' => 'array',
    ];
    public function sessions()
    {
        return $this->hasMany(\App\Models\Session::class, 'specialist_id');
    }

    /**
     * Get the user that owns the specialist.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bookings for the specialist.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'specialist_id');
    }


    /**
     * Get the reviews for the specialist.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the services that the specialist provides.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'specialist_service', 'specialist_id', 'service_id')
            ->withTimestamps();
    }

    /**
     * Get the categories that belong to the specialist.
     */
    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'specialist_category', 'specialist_id', 'category_id')
            ->withTimestamps();
    }

    /**
     * Get the packages that the specialist provides.
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'specialist_package', 'specialist_id', 'package_id')
            ->withTimestamps();
    }
}
