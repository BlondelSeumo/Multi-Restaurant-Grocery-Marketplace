<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrandImport extends BaseImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable;

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

            if (!data_get($row, 'title')) {
                continue;
            }

            $brand = Brand::updateOrCreate(['title' => data_get($row, 'title')], [
                'title'     => data_get($row, 'title', ''),
                'active'    => data_get($row, 'active') === 'active' ? 1 : 0,
            ]);

            $this->downloadImages($brand, data_get($row, 'img_urls', ''));
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
