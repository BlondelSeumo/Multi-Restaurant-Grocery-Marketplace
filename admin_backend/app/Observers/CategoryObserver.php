<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ShopCategory;
use Exception;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     *
     * @param Category $category
     * @return void
     * @throws Exception
     */
    public function creating(Category $category): void
    {
        $category->uuid = Str::uuid();
    }

    /**
     * Handle the Category "created" event.
     *
     * @param Category $category
     * @return void
     */
    public function created(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param Category $category
     * @return void
     */
    public function updated(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(Category $category): void
    {
        foreach (ShopCategory::where('category_id', $category->id)->get() as $shopCategory) {
            $shopCategory->delete();
        }

        foreach (Product::where('category_id', $category->id)->get() as $product) {
            $product->update([
                'category_id' => null
            ]);
        }
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param Category $category
     * @return void
     */
    public function restored(Category $category): void
    {
    }
}
