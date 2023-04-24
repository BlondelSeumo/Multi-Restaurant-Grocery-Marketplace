<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersRevenueReportExport extends BaseExport implements FromCollection, WithHeadings
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn($row) => $this->tableBody($row));
    }

    public function headings(): array
    {
        return [
            'Date',
            'Orders',
            'Shipping',
            'Returns',
            'Taxes',
            'Total sales',
        ];
    }

    private function tableBody($row): array
    {
        return [
            'time'          => data_get($row, 'time', 0),
            'count'         => data_get($row, 'count', 0),
            'delivered_sum' => data_get($row, 'delivered_sum', 0),
            'canceled_sum'  => data_get($row, 'canceled_sum', 0),
            'tax'           => data_get($row, 'tax', 0),
            'total_price'   => data_get($row, 'total_price', 0),
        ];
    }
}
