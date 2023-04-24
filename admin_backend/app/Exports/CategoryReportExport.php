<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryReportExport extends BaseExport implements FromCollection, WithHeadings
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn(Collection $row) => $this->tableBody($row));
    }

    public function headings(): array
    {
        return [
            'Category',
            'Item sold',
            'Net sales',
            'Products',
            'Orders',
        ];
    }

    private function tableBody(Collection $row): array
    {
        return [
            'category'          => data_get($row, 'category'),
            'quantity'          => data_get($row, 'quantity'),
            'price'             => data_get($row, 'price'),
            'products_count'    => data_get($row, 'products_count'),
            'count'             => data_get($row, 'count'),
        ];
    }
}
