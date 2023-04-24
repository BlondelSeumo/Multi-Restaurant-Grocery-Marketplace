<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport extends BaseExport implements FromCollection, WithHeadings
{
    protected array $filter;

    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }

    public function collection(): Collection
    {
        $language = Language::where('default', 1)->first();

        $products = Product::filter($this->filter)
            ->with([
                'category.translation'  => fn($q) => $q->where('locale', data_get($this->filter, 'language'))
                    ->orWhere('locale', $language),

                'unit.translation'      => fn($q) => $q->where('locale', data_get($this->filter, 'language'))
                    ->orWhere('locale', $language),

                'translation'           => fn($q) => $q->where('locale', data_get($this->filter, 'language'))
                    ->orWhere('locale', $language),

                'brand:id,title',
            ])
            ->orderBy('id')
            ->get();

        return $products->map(fn(Product $product) => $this->tableBody($product));
    }

    public function headings(): array
    {
        return [
            '#',
            'Uu Id',
            'Product Title',
            'Product Description',
            'Shop Id',
            'Shop Name',
            'Category Id',
            'Category Title',
            'Brand Id',
            'Brand Title',
            'Unit Id',
            'Unit Title',
            'Keywords',
            'Tax',
            'Active',
            'Bar Code',
            'Qr Code',
            'Status',
            'Min Qty',
            'Max Qty',
            'Price',
            'Quantity',
            'Img Urls',
            'Created At',
        ];
    }

    private function tableBody(Product $product): array
    {
        $stock = $product->stocks->first();

        return [
           'id'             => $product->id,//0
           'uuid'           => $product->uuid,//1
           'title'          => data_get($product->translation, 'title', ''),//2
           'description'    => data_get($product->translation, 'description', ''),//3
           'shop_id'        => $product->shop_id,//4
           'shop_title'     => data_get(optional($product->shop)->translation, 'title', ''),//5
           'category_id'    => $product->category_id ?? 0,//6
           'category_title' => data_get(optional($product->category)->translation, 'title', ''),//7
           'brand_id'       => $product->brand_id ?? 0,//8
           'brand_title'    => optional($product->brand)->title ?? 0,//9
           'unit_id'        => $product->unit_id ?? 0,//10
           'unit_title'     => data_get(optional($product->unit)->translation, 'title', ''),//11
           'keywords'       => $product->keywords ?? '',//12
           'tax'            => $product->tax ?? 0,//13
           'active'         => $product->active ? 'active' : 'inactive',//14
           'bar_code'       => $product->bar_code ?? '',//15
           'qr_code'        => $product->qr_code ?? '',//16
           'status'         => $product->status ?? Product::PENDING,//17
           'min_qty'        => $product->min_qty ?? 0,//18
           'max_qty'        => $product->max_qty ?? 0,//19
           'price'          => data_get($stock, 'price', 0),//20
           'quantity'       => data_get($stock, 'quantity', 0),//21
           'img_urls'       => $this->imageUrl($product->galleries) ?? '',//22
           'created_at'     => $product->created_at ?? date('Y-m-d H:i:s'),//23
        ];
    }
}
