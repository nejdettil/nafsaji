<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    /**
     * الخصائص التي يمكن تعيينها بشكل جماعي
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'position',
        'content',
        'avatar',
        'rating',
        'is_active',
        'order',
    ];

    /**
     * الخصائص التي يجب تحويلها
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * نطاق للشهادات النشطة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق للشهادات المرتبة حسب الترتيب
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
