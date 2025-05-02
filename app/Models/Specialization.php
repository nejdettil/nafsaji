<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the specialists associated with the specialization.
     */
    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(Specialist::class, 'specialist_specialization', 'specialization_id', 'specialist_id')
            ->withTimestamps();
    }

    /**
     * Get the services associated with the specialization.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_specialization', 'specialization_id', 'service_id')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active specializations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
