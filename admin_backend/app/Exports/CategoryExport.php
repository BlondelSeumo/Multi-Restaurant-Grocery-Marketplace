<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport extends BaseExport implements FromCollection, WithHeadings
{
    protected string $language;

    public function __construct(string $language) {
        $this->language = $language;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $language = Language::where('default', 1)->first();
        if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
            abort(403);
        }
        $categories = Category::with([
            'translation' => fn($q) => $q->where('locale', $this->language)
                ->orWhere('locale', data_get($language, 'locale')),
        ])
            ->where('type', Category::MAIN)
            ->orderBy('id')
            ->get();

        return $categories->map(fn(Category $category) => $this->tableBody($category));
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            '#',
            'Uu Id',
            'Keywords',
            'Parent Id',
            'Title',
            'Description',
            'Active',
            'Type',
            'Img Urls',
        ];
    }

    /**
     * @param Category $category
     * @return array
     */
    private function tableBody(Category $category): array
    {
        return [
            'id'            => $category->id,//0
            'uuid'          => $category->uuid,//1
            'keywords'      => $category->keywords,//2
            'parent_id'     => $category->parent_id,//3
            'title'         => data_get($category->translation, 'title', ''),//4
            'description'   => data_get($category->translation, 'description', ''),//5
            'active'        => $category->active ? 'active' : 'inactive',//6
            'type'          => data_get(Category::TYPES_VALUES, $category->type, 'main'),//7
            'img_urls'      => $this->imageUrl($category->galleries),//9
        ];
    }
}
