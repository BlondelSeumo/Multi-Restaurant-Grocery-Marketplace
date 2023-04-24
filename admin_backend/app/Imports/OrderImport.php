<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport extends BaseImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable;

    private string $language;
    private ?int $shopId;

    public function __construct(string $language, ?int $shopId = null)
    {
        $this->language = $language;
        $this->shopId   = $shopId;
    }

    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        foreach ($collection as $row) {

            $location   = explode(',', data_get($row, 'location', ''));

            Order::updateOrCreate([
                'user_id'                => data_get($row,'user_id'),
                'username'               => data_get($row,'username'),
                'total_price'            => data_get($row,'total_price'),
                'currency_id'            => data_get($row,'currency_id'),
                'rate'                   => data_get($row,'rate'),
                'note'                   => data_get($row,'note'),
                'shop_id'                => data_get($row,'shop_id'),
                'tax'                    => data_get($row,'tax') > 0 ? data_get($row,'tax') : 0,
                'commission_fee'         => data_get($row,'commission_fee'),
                'status'                 => data_get($row,'status'),
                'delivery_fee'           => data_get($row,'delivery_fee') > 0 ? data_get($row,'delivery_fee') : 0,
                'deliveryman'            => data_get($row,'deliveryman'),
                'delivery_date'          => data_get($row,'delivery_date'),
                'delivery_time'          => data_get($row,'delivery_time'),
                'total_discount'         => data_get($row,'total_discount'),
                'location'               => [
                    'latitude'  => data_get($location, 0),
                    'longitude' => data_get($location, 1),
                ],
                'address'                => data_get($row, 'address'),
                'delivery_type'          => data_get($row, 'delivery_type'),
                'phone'                  => data_get($row, 'phone'),
            ]);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 10;
    }
}
