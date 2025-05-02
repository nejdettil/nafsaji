<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialist_id',
        'service_id',
        'package_id',
        'booking_date',
        'duration',
        'amount',
        'status',
        'notes',
        'cancellation_reason',
        'is_paid',
        'meeting_link',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'datetime',
        'is_paid' => 'boolean',
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialist that owns the booking.
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * Get the service that owns the booking.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the package that owns the booking.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the session associated with the booking.
     */
    public function session(): HasOne
    {
        return $this->hasOne(Session::class);
    }

    /**
     * Get the payment associated with the booking.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
