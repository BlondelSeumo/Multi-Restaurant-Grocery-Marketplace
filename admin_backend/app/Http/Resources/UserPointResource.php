<?php

namespace App\Http\Resources;

use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var UserPoint $this */
        return [
            'user_id'       => $this->user_id,
            'price'         => $this->price,
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
