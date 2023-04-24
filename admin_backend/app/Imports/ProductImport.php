<?php

namespace App\Imports;

use App\Models\Language;
use App\Models\Product;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class ProductImport extends BaseImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable;

    private ?int $shopId;
    private string $language;

    public function __construct(?int $shopId, string $language)
    {
        $this->shopId   = $shopId;
        $this->language = $language;
    }

    /**
     * @param Collection $collection
     * @return void
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {
        $language = Language::where('default', 1)->first();
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        foreach ($collection as $row) {

            DB::transaction(function () use ($row, $language) {

                $data = [
                    'shop_id'       => $this->shopId ?? data_get($row,'shop_id'),
                    'category_id'   => data_get($row, 'category_id'),
                    'brand_id'      => data_get($row, 'brand_id'),
                    'unit_id'       => data_get($row, 'unit_id'),
                    'keywords'      => data_get($row, 'keywords', ''),
                    'tax'           => data_get($row, 'tax', 0),
                    'active'        => data_get($row, 'active') === 'active' ? 1 : 0,
                    'bar_code'      => data_get($row, 'bar_code', ''),
                    'qr_code'       => data_get($row, 'qr_code', ''),
                    'status'        => in_array(data_get($row, 'status'), Product::STATUSES) ? data_get($row, 'status') : Product::PENDING,
                    'min_qty'       => data_get($row, 'min_qty', 1),
                    'max_qty'       => data_get($row, 'max_qty', 1000000),
                ];

                $product = Product::withTrashed()->updateOrCreate($data, $data + [
                    'deleted_at' => null
                ]);

                $this->downloadImages($product, data_get($row, 'img_urls', ''));

                if (!empty(data_get($row, 'product_title'))) {
                    $product->translation()->delete();

                    $product->translation()->create([
                        'locale'        => $this->language ?? $language,
                        'title'         => data_get($row, 'product_title', ''),
                        'description'   => data_get($row, 'product_description', '')
                    ]);
                }

                if (!empty(data_get($row, 'price')) || !empty(data_get($row, 'quantity'))) {
                    $product->stocks()->delete();

                    $product->stocks()->create([
                        'price'     => data_get($row, 'price')    > 0 ? data_get($row, 'price') : 0,
                        'quantity'  => data_get($row, 'quantity') > 0 ? data_get($row, 'quantity') : 0,
                    ]);
                }
            });

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
