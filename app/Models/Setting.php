<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get settings by group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get public settings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * Set setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param bool $isPublic
     * @return \App\Models\Setting
     */
    public static function setValue($key, $value, $group = 'general', $type = 'string', $isPublic = false)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return self::create([
                'key' => $key,
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'is_public' => $isPublic,
            ]);
        }

        $setting->update([
            'value' => $value,
            'group' => $group,
            'type' => $type,
            'is_public' => $isPublic,
        ]);

        return $setting;
    }
}
