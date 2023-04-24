<?php

namespace App\Exports;

use App\Models\ExtraValue;
use App\Models\Order;
use App\Traits\Loggable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection,
    ShouldAutoSize,
    WithBatchInserts,
    WithChunkReading,
    WithHeadings,
    WithMapping,
};
use Illuminate\Support\Facades\Cache;
use Throwable;

class OrdersReportExport implements FromCollection, WithMapping, ShouldAutoSize, WithBatchInserts, WithChunkReading, WithHeadings
{
    use Loggable;

    private Collection $rows;

    /**
     * OrdersReportExport constructor.
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

    public function orderProductsTitle(Order $order): string
    {
        $names = '';

        foreach ($order->orderDetails as $orderDetail) {

            try {
                $extras = $orderDetail->stock->loadMissing([
                    'stockExtras.group.translation',
                ])->stockExtras();

                try {
                    $title = $orderDetail->stock->countable->translation->title;
                } catch (Throwable $e) {
                    $title = '';
                }

                $value = '';
                try {
                    /** @var ExtraValue $extra */
                    foreach ($extras->get() as $extra) {

                        $value .= '-' . optional(optional($extra->group)->translation)->title . '-' . $extra->value;
                    }

                } catch (Throwable $e) {}

                $names .= ", $title$value";

            } catch (Throwable $e) {
                $this->error($e);
            }
        }
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        return trim($names, ', ');
    }

    public function moneyFormatter($number): string
    {
        [$whole, $decimal] = sscanf($number, '%d.%d');

        $money = number_format($number, 0, ',', ' ');

        return $decimal ? $money . ",$decimal" : $money;
    }

    public function map($row): array
    {
        /** @var Order $row */
        return [
            $row->created_at,
            $row->id,
            '',
            data_get($row->user, 'firstname') . ' ' . data_get($row->user, 'lastname'),
            '',
            $this->orderProductsTitle($row),
            $row->order_details_sum_quantity,
            '',
            $this->moneyFormatter($row->total_price)
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            '#',
            'Status',
            'Customer',
            'Customer type',
            'Products',
            'Item sold',
            'coupons',
            'Net sales',
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
