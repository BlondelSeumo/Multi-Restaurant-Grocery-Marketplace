<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection,
    ShouldAutoSize,
    WithBatchInserts,
    WithChunkReading,
    WithHeadings,
    WithMapping,
};

class ProductsReportExport implements FromCollection, WithMapping, ShouldAutoSize, WithBatchInserts, WithChunkReading, WithHeadings
{
    private Collection $rows;

    /**
     * ProductsReportExport constructor.
     *
     * @param Collection $rows
     */
    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->rows;
    }

    public function map($row): array
    {
        /** @var Product $row */
        return [
            $row->translation_title,
            $row->bar_code,
            $row->items_sold,
            moneyFormatter($row->net_sales),
            $row->orders_count,
            $row->category->self_and_parent_title,
            $row->variations,
            $row->active,
            $row->stocks_total,
        ];
    }

    public function headings(): array
    {
        return [
            'Product title',
            'Sku',
            'Items sold',
            'Net sales',
            'Order',
            'Category',
            'Variations',
            'Status',
            'Stock',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
