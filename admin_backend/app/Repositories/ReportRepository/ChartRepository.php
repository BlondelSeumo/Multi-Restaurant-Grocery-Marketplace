<?php

namespace App\Repositories\ReportRepository;

class ChartRepository
{
    /**
     * @param $chart
     * @param string $key
     * @return array
     */
    final static function chart($chart, string $key): array
    {
        $result = [];

        foreach ($chart as $item) {

            $time = data_get($item, 'time');

            if (empty($time)) {
                continue;
            }

            if (!data_get($result, $time)) {

                $result[$time] = [
                    'time'  => $time,
                    $key    => $key === 'count' && data_get($item, $key) === 1 ? 1 : data_get($item, $key)
                ];

                continue;

            }

            $qty = data_get($result, "$time.$key") + ($key === 'count' && data_get($item, $key) === 1 ? 1 : data_get($item, $key));

            $result[$time] = [
                'time'  => $time,
                $key    => $qty
            ];

        }

        return array_values($result);
    }
}
