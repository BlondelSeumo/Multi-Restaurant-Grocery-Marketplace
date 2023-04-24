<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Shop;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var UserActivity|JsonResource $this */

        return [
            'id'            => $this->when($this->id, $this->id),
            'user_id'       => $this->when($this->user_id, $this->user_id),
            'model_type'    => $this->when($this->model_type, $this->model_type),
            'model_id'      => $this->when($this->model_id, $this->model_id),
            'type'          => $this->when($this->type, $this->type),
            'value'         => $this->when($this->value, $this->value),
            'ip'            => $this->when($this->ip, $this->ip),
            'device'        => $this->when($this->device, $this->device),
            'agent'         => $this->when($this->agent, $this->agent),
            'created_at'    => $this->when($this->created_at, $this->created_at),

            'user'          => UserResource::make($this->whenLoaded('user')),

            'product'       => $this->when($this->model_type === Product::class,
                                ProductResource::make($this->whenLoaded('model'))
            ),
            'shop'          => $this->when($this->model_type === Shop::class,
                                ShopResource::make($this->whenLoaded('model'))
            ),
        ];
    }

}
