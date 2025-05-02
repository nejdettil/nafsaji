<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'specialist_id',
        'service_id',
        'booking_id',
        'rating',
        'comment',
        'is_anonymous',
        'is_verified',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'decimal:1',
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialist that the review is for.
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * Get the service that the review is for.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the booking associated with the review.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
