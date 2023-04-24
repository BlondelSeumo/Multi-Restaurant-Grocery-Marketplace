<?php

namespace App\Http\Resources;

use App\Models\DeliveryManSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var DeliveryManSetting|JsonResource $this */

        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'type_of_technique' => $this->type_of_technique,
            'brand'             => $this->brand,
            'model'             => $this->model,
            'number'            => $this->number,
            'color'             => $this->color,
            'online'            => (boolean)$this->online,
            'location'          => $this->location,
            'created_at'        => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'        => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'        => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
            // Relations
            'deliveryMan'       => UserResource::make($this->whenLoaded('deliveryMan')),
            'galleries'         => GalleryResource::collection($this->whenLoaded('galleries')),
        ];
    }
}
