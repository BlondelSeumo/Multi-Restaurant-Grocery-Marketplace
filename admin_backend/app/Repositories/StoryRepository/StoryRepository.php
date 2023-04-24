<?php

namespace App\Repositories\StoryRepository;

use App\Models\Language;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Story;
use App\Repositories\CoreRepository;
use Illuminate\Database\Eloquent\Builder;

class StoryRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Story::class;
    }

    /**
     * @param array $data
     * @param string $type
     * @return mixed
     */
    public function index(array $data = [], string $type = 'paginate'): mixed
    {
        /** @var Story $stories */
        $stories = $this->model();

        $paginate = data_get([
            'paginate'          => 'paginate',
            'simplePaginate'    => 'simplePaginate'
        ], $type, 'paginate');

        return $stories
            ->with([
                'product'               => fn ($q) => $q->select(['id', 'uuid']),
                'product.translation'   => fn ($q) => $q->select('id', 'product_id', 'locale', 'title')
                    ->where('locale', $this->language),
                'shop'                  => fn ($q) => $q->select(['id', 'uuid', 'user_id', 'logo_img']),
                'shop.translation'      => fn ($q) => $q->select('id', 'shop_id', 'locale', 'title')
                    ->where('locale', $this->language),
            ])
            ->select([
                'id',
                'product_id',
                'shop_id',
                'active',
                'file_urls',
            ])
            ->when(data_get($data, 'shop_id'), fn(Builder $q, $shopId) => $q->where('shop_id', $shopId))
            ->when(data_get($data, 'product_id'), fn(Builder $q, $productId) => $q->where('product_id', $productId))
            ->when(isset($data['active']), fn($q) => $q->where('active', $data['active']))
            ->where(function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->where(fn($q) => $q->where('updated_at', '>=', date('Y-m-d 00:00:01'))
                        ->where('updated_at', '<=', date('Y-m-d 23:59:59'))
                    )
                    ->orWhere(fn($q) => $q->where('created_at', '>=', date('Y-m-d 00:00:01'))
                        ->where('created_at', '<=', date('Y-m-d 23:59:59'))
                    );
                });
            })
            ->orderBy(data_get($data, 'column', 'id'), data_get($data, 'sort', 'desc'))
            ->$paginate(data_get($data, 'perPage', 15));
    }

    public function list(array $data = []): array
    {
//        $data['open'] = 1;
//        $data['status'] = 'approved';
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        $shopsStories = Shop::filter($data)->with([
            'products:id,shop_id,uuid,active,addon,status',
            'products.stories:id,file_urls,product_id,created_at,updated_at',
            'products.translation' => fn ($q) => $q->where('locale', $this->language)
                ->orWhere('locale', $locale)
                ->select('id', 'product_id', 'locale', 'title'),
            'translation' => fn ($q) => $q->where('locale', $this->language)
                ->orWhere('locale', $locale)
                ->select('id', 'shop_id', 'locale', 'title'),
        ])
            ->whereHas('products', function (Builder $query) use ($data) {
                $query->when(data_get($data, 'free'), function ($q) {
                    $q->whereHas('stocks', fn($b) => $b->where('price', '=', 0));
                })->where('active', 1)->where('addon', 0)
                    ->where('status', Product::PUBLISHED);
            })
            ->whereHas('products.stories', function (Builder $query) {
                $query
                    ->where('active', 1)
                    ->whereJsonLength('file_urls', '>', 0);
                //->where(function (Builder $query) {
                //                        $query->where(function (Builder $query) {
                //                            $query->where(fn($q) => $q->where('updated_at', '>=', date('Y-m-d 00:00:01'))
                //                                ->where('updated_at', '<=', date('Y-m-d 23:59:59'))
                //                            )
                //                            ->orWhere(fn($q) => $q->where('created_at', '>=', date('Y-m-d 00:00:01'))
                //                                ->where('created_at', '<=', date('Y-m-d 23:59:59'))
                //                            );
                //                        });
                //                    })
        })
            ->select(['id', 'uuid', 'logo_img'])
            ->simplePaginate(data_get($data, 'perPage', 100));

        $shops = collect();

        foreach ($shopsStories as $shopStories) {
            /** @var Shop $shopStories */

            if (!$shopStories->id) {
                continue;
            }

            $shops[$shopStories->id] = [];

            foreach ($shopStories->products as $product) {

                if (!$product->id) {
                    // || !$product->active || !$product->addon | $product->status !== Product::PUBLISHED
                    continue;
                }

                foreach ($product->stories as $story) {

//                    if (!$story->file_urls || ($story->created_at <= date('Y-m-d 23:59:59') || $story->updated_at <= date('Y-m-d 23:59:59') )) {
//                        $story->delete();
//                        continue;
//                    }

                    foreach ($story->file_urls as $file_url) {

                        $shops[$shopStories->id] = [
                            ...$shops[$shopStories->id],
                            [
                                'shop_id'       => $shopStories->id,
                                'logo_img'      => $shopStories->logo_img,
                                'title'         => data_get($shopStories->translation, 'title'),
                                'firstname'     => data_get($shopStories->seller, 'firstname'),
                                'lastname'      => data_get($shopStories->seller, 'lastname'),
                                'avatar'        => data_get($shopStories->seller, 'img'),
                                'product_uuid'  => $product->uuid,
                                'product_title' => data_get($product->translation, 'title'),
                                'url'           => $file_url,
                                'created_at'    => optional($story->created_at)->format('Y-m-d H:i:s'),
                                'updated_at'    => optional($story->updated_at)->format('Y-m-d H:i:s'),
                            ]
                        ];

                    }

                }

            }

        }

        return $shops?->count() > 0 ? array_values($shops->toArray()) : [];
    }

    public function show(Story $story): Story
    {
        return $story->load([
            'product',
            'product.translation' => fn($q) => $q->where('locale', $this->language),
            'shop.translation'  => fn ($q) => $q->select('id', 'shop_id', 'locale', 'title')
                ->where('locale', $this->language),
        ]);
    }
}
