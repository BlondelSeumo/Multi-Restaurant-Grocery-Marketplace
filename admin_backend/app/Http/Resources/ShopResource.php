<?php

namespace App\Http\Resources;

use App\Http\Resources\Bonus\ShopBonusResource;
use App\Models\Shop;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Shop|JsonResource $this */
        $isSeller = auth('sanctum')->check() && auth('sanctum')->user()?->hasRole('seller');
        $isRecommended = in_array($this->id, array_keys(Cache::get('shop-recommended-ids', [])));

        return [
            'id'                => $this->when($this->id, $this->id),
            'uuid'              => $this->when($this->uuid, $this->uuid),
            'discounts_count'   => $this->whenLoaded('discounts', $this->discounts_count),
            'user_id'           => $this->when($this->user_id, $this->user_id),
            'price'             => $this->when($this->rate_price, $this->rate_price),
            'price_per_km'      => $this->when($this->rate_price_per_km, $this->rate_price_per_km),
            'tax'               => $this->when($this->tax, $this->tax),
            'percentage'        => $this->when($this->percentage, $this->percentage),
            'phone'             => $this->when($this->phone, $this->phone),
            'show_type'         => $this->when($this->show_type, $this->show_type),
            'open'              => (bool)$this->open,
            'visibility'        => $this->visibility, // (bool)
            'background_img'    => $this->when($this->background_img, $this->background_img),
            'logo_img'          => $this->when($this->logo_img, $this->logo_img),
            'min_amount'        => $this->when($this->min_amount, $this->min_amount),
            'mark'              => $this->when($this->mark, $this->mark),
            'is_recommended'    => $this->when($isRecommended, $isRecommended),
            'status'            => $this->when($this->status, $this->status),
            'status_note'       => $this->when($this->status_note, $this->status_note),
            'type'              => $this->when($this->type, data_get(Shop::TYPES, $this->type)),
            'avg_rate'          => $this->when($this->avg_rate, $this->avg_rate),
            'delivery_time'     => $this->when($this->delivery_time, $this->delivery_time),
            'invite_link'       => $this->when($isSeller, "/shop/invitation/$this->uuid/link"),
            'rating_avg'        => $this->when($this->reviews_avg_rating, $this->reviews_avg_rating),
            'reviews_count'     => $this->when($this->reviews_count, (int) $this->reviews_count),
            'orders_count'      => $this->when($this->orders_count, (int) $this->orders_count),
            'created_at'        => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
            'location'          => $this->when(data_get($this->location, 'latitude'), [
                'latitude'      => data_get($this->location, 'latitude'),
                'longitude'     => data_get($this->location, 'longitude'),
            ]),
            'products_count'    => $this->whenLoaded('products', $this->products_count, 0),
            'translation'       => TranslationResource::make($this->whenLoaded('translation')),
            'tags'              => ShopTagResource::collection($this->whenLoaded('tags')),
            'translations'      => TranslationResource::collection($this->whenLoaded('translations')),
            'locales'           => $this->relationLoaded('translations') ? $this->translations->pluck('locale')->toArray() : [],
            'seller'            => UserResource::make($this->whenLoaded('seller')),
            'subscription'      => ShopSubscriptionResource::make($this->whenLoaded('subscription')),
            'categories'        => CategoryResource::collection($this->whenLoaded('categories')),
            'bonus'             => ShopBonusResource::make($this->whenLoaded('bonus')),
            'discount'          => SimpleDiscountResource::collection($this->whenLoaded('discounts')),
            'shop_payments'     => ShopPaymentResource::collection($this->whenLoaded('shopPayments')),
            'shop_working_days' => ShopWorkingDayResource::collection($this->whenLoaded('workingDays')),
            'shop_closed_date'  => ShopClosedDateResource::collection($this->whenLoaded('closedDates')),
        ];
    }
}
