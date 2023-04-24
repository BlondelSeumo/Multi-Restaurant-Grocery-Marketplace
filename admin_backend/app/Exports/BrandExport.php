<?php

namespace App\Exports;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BrandExport extends BaseExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $brands = Brand::orderBy('id')->get();
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return $brands->map(fn (Brand $brand) => $this->tableBody($brand));
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            '#',
            'Uu Id',
            'Title',
            'Active',
            'Img Urls',
        ];
    }

    /**
     * @param Brand $brand
     * @return array
     */
    private function tableBody(Brand $brand): array
    {
        return [
            'id'        => $brand->id,//0
            'uuid'      => $brand->uuid,//1
            'title'     => $brand->title,//2
            'active'    => $brand->active ? 'active' : 'inactive',//3
            'img_urls'  => $this->imageUrl($brand->galleries),//4
        ];
    }
}
