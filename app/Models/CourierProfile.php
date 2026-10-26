<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierProfile extends Model
{
    protected $fillable = ['user_id', 'latitude', 'longitude', 'radius_km', 'is_available', 'location_updated_at'];

    protected $casts = [
        'latitude'            => 'float',
        'longitude'           => 'float',
        'is_available'        => 'boolean',
        'location_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getHasLocationAttribute(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
