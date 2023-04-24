<?php

namespace App\Helpers;

use App\Models\Shop;
use Illuminate\Pagination\LengthAwarePaginator;

class Utility
{
    /* Pagination for array */
    public static function paginate($items, $perPage, $page = null, $options = []): LengthAwarePaginator
    {
        return new LengthAwarePaginator($items?->forPage($page, $perPage), $items?->count() ?? 0, $perPage, $page, $options);
    }

    /**
     * @param float|null $km
     * @param Shop|null $shop
     * @param float|null $rate
     * @return float|null
     */
    public function getPriceByDistance(?float $km, ?Shop $shop, ?float $rate): ?float
    {
        $price      = data_get($shop, 'price', 0);
        $pricePerKm = data_get($shop, 'price_per_km');

        return round(($price + ($pricePerKm * $km)) * $rate, 2);
    }

    /**
     * @param array $origin, Адрес селлера (откуда)
     * @param array $destination, Адрес клиента (куда)
     * @return float|int|null
     */
    public function getDistance(array $origin, array $destination): float|int|null
    {

        if (count($origin) !== 2 && count($destination) !== 2) {
            return 0;
        }

        $originLat          = $this->toRadian(data_get($origin, 'latitude'));
        $originLong         = $this->toRadian(data_get($origin, 'longitude'));
        $destinationLat     = $this->toRadian(data_get($destination, 'latitude'));
        $destinationLong    = $this->toRadian(data_get($destination, 'longitude'));

        $deltaLat           = $destinationLat - $originLat;
        $deltaLon           = $originLong - $destinationLong;

        $delta              = pow(sin($deltaLat / 2), 2);
        $cos                = cos($destinationLong) * cos($destinationLat);

        $sqrt               = ($delta + $cos * pow(sin($deltaLon / 2), 2));
        $asin               = 2 * asin(sqrt($sqrt));

        $earthRadius        = 6371;

        return (string)$asin != 'NAN' ? round($asin * $earthRadius, 2) : 1;
    }

    private function toRadian($degree = 0): ?float
    {
        return $degree * pi() / 180;
    }

    public static function pointInPolygon(array $point, array $vs): bool
    {
        $x = data_get($point, 'latitude');
        $y = data_get($point, 'longitude');

        $inside = false;

        for ($i = 0, $j = count($vs) - 1; $i < count($vs); $j = $i++) {

            $xi = $vs[$i][0];
            $yi = $vs[$i][1];
            $xj = $vs[$j][0];
            $yj = $vs[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }

        }

        return $inside;
    }

    public static function groupRating($reviews): array
    {
        $result = [
            1 => 0.0,
            2 => 0.0,
            3 => 0.0,
            4 => 0.0,
            5 => 0.0,
        ];

        foreach ($reviews as $review) {

            switch (true) {
                case data_get($review, 'rating') <= 1:
                    $result[1] += data_get($review, 'count');
                    break;
                case data_get($review, 'rating') <= 2:
                    $result[2] += data_get($review, 'count');
                    break;
                case data_get($review, 'rating') <= 3:
                    $result[3] += data_get($review, 'count');
                    break;
                case data_get($review, 'rating') <= 4:
                    $result[4] += data_get($review, 'count');
                    break;
                case data_get($review, 'rating') <= 5:
                    $result[5] += data_get($review, 'count');
                    break;
            }

        }

        return $result;
    }
}
