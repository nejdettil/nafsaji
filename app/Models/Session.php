<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'specialist_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'specialist_notes',
        'summary',
        'recommendations',
        'rating',
        'review',
        'recording_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Get the booking that owns the session.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialist that owns the session.
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc');
    }
}
