<?php

namespace App\Http\Resources;

use App\Models\OrderRefund;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRefundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var OrderRefund|JsonResource $this */
        return [
            'id'                    => $this->id,
            'status'                => $this->status,
            'cause'                 => $this->cause,
            'answer'                => $this->answer,
            'created_at'            => $this->when($this->created_at, optional($this->created_at)->format('Y-m-d H:i:s')),
            'updated_at'            => $this->when($this->updated_at, optional($this->updated_at)->format('Y-m-d H:i:s')),
            'deleted_at'            => $this->when($this->deleted_at, optional($this->deleted_at)->format('Y-m-d H:i:s')),

            'order'                 => OrderResource::make($this->whenLoaded('order')),
            'galleries'             => GalleryResource::collection($this->whenLoaded('galleries')),
        ];
    }
}
