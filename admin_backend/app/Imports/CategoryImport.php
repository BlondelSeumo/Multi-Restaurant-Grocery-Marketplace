<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class CategoryImport extends BaseImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable;

    private string $language;

    public function __construct(string $language)
    {
        $this->language = $language;
    }

    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        $language = Language::where('default', 1)->first();
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }

        foreach ($collection as $row) {

            $type = data_get($row, 'type');

            try {

                DB::transaction(function () use ($row, $language, $type) {

                    $category = Category::withTrashed()->firstOrCreate([
                        'keywords'  => data_get($row, 'keywords', ''),
                        'type'      => empty($type) ? Category::MAIN : data_get(Category::TYPES, $type, Category::MAIN),
                        'parent_id' => data_get($row, 'parent_id') > 0 ? data_get($row, 'parent_id') : 0,
                        'active'    => data_get($row, 'active') === 'active' ? 1 : 0,
                    ], [
                        'deleted_at' => null
                    ]);

                    if (!data_get($row, 'title')) {
                        return true;
                    }

                    $category->translation()->delete();
                    $category->translation()->create([
                        'locale'        => $this->language ?? $language->locale,
                        'title'         => data_get($row, 'title'),
                        'description'   => data_get($row, 'description', '')
                    ]);

                    $this->downloadImages($category, data_get($row, 'img_urls', ''));

                    return true;
                });

            } catch (Throwable $e) {
                $this->error($e);
            }

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
