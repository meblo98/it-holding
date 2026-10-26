<?php

namespace App\Services;

use Illuminate\Support\Str;

class GeoDistance
{
    /** Distance à vol d'oiseau en kilomètres (formule de haversine). */
    public static function km(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return round($earth * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    /** Position approximative d'une ville connue, sinon null. @return array{0:float,1:float}|null */
    public static function cityPoint(?string $city): ?array
    {
        if (!$city) {
            return null;
        }

        $key = Str::slug(Str::ascii(trim($city)));

        return config('delivery.cities.' . $key);
    }
}
