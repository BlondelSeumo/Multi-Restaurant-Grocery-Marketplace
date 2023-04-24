<?php

namespace App\Exports;

use App\Models\Shop;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShopExport extends BaseExport implements FromCollection, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $shops = Shop::orderBy('id')->get();

        return $shops->map(fn (Shop $shop) => $this->tableBody($shop));
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            '#',
            'uuid',
            'User Id',
            'Tax',
            'Percentage',
            'Location',
            'Phone',
            'Show Type',
            'Open',
            'Img Urls',
            'Min Amount',
            'Status',
            'Status Note',
            'Created At',
            'Mark',
            'Take',
            'Delivery Time',
            'Type',
            'Delivery Price',
            'Delivery Price Per Km',
        ];
    }

    /**
     * @param Shop $shop
     * @return array
     */
    private function tableBody(Shop $shop): array
    {

        $from = data_get($shop->delivery_time, 'from', '');
        $to   = data_get($shop->delivery_time, 'to', '');
        $type = data_get($shop->delivery_time, 'type', '');

        return [
            'id'                => $shop->id, //0
            'uuid'              => $shop->uuid, //1
            'user_id'           => $shop->user_id, //2
            'tax'               => $shop->tax, //3
            'percentage'        => $shop->percentage, //4
            'location'          => implode(',', $shop->location), //5
            'phone'             => $shop->phone, //6
            'show_type'         => $shop->show_type, //7
            'open'              => $shop->open, //8
            'img_urls'          => $this->imageUrl($shop->galleries), //10
            'min_amount'        => $shop->min_amount, //12
            'status'            => $shop->status, //13
            'status_note'       => $shop->status_note, //14
            'created_at'        => $shop->created_at ?? date('Y-m-d H:i:s'),//23
            'mark'              => $shop->mark, //18
            'take'              => $shop->take, //19
            'delivery_time'     => "from: $from, to: $to, type: $type", //20
            'type'              => data_get(Shop::TYPES, $shop->type, 'shop'), //21
            'price'             => $shop->price, //22
            'price_per_km'      => $shop->price_per_km, //23
        ];
    }
}
