<?php

namespace App\Exports;

use App\Models\Language;
use App\Models\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport extends BaseExport implements FromCollection, WithHeadings
{
    protected string $defaultLanguage;
    protected string $language;

    public function __construct()
    {
        $this->defaultLanguage = data_get(
            Language::where('default', 1)->first(['locale', 'default']), 'locale'
        );
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        $this->language = request(
            'lang',
            data_get(Language::where('default', 1)->first(['locale', 'default']), 'locale')
        );
    }

    public function collection(): Collection
    {
        $stocks = Stock::with([
                'countable.category.translation'  => fn($q) => $q->where('locale', $this->language)
                    ->orWhere('locale', $this->defaultLanguage),

                'countable.translation' => fn($q) => $q->where('locale', $this->language)
                    ->orWhere('locale', $this->defaultLanguage),
                'stockExtras.group',
            ])
            ->whereHas('countable')
            ->orderBy('id')
            ->get();

        return $stocks->map(fn(Stock $stock) => $this->tableBody($stock));
    }

    public function headings(): array
    {
        return [
            '#',
            'Type',
            'Type Id',
            'Title',
            'Price',
            'Quantity',
            'Value Ids',
            'Values',
        ];
    }

    private function tableBody(Stock $stock): array
    {

        return [
           'id'             => $stock->id, //0
           'countable_type' => Str::after($stock->countable_type, 'App\Models\\'), //1
           'countable_id'   => $stock->countable_id, //2
           'title'          => data_get(optional($stock->countable), 'translation.title'), //2
           'price'          => $stock->price, //3
           'quantity'       => $stock->quantity, //4
           'extra_value_id' => $stock->stockExtras->implode('id', ','), //4
           'extra_values'   => $stock->stockExtras->implode('value', ','), //4
        ];
    }
}
