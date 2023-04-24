<?php

namespace App\Http\Resources;

use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var EmailSetting|JsonResource $this */
        return [
            'id'            => $this->id,
            'smtp_auth'     => $this->smtp_auth,
            'smtp_debug'    => $this->smtp_debug,
            'host'          => $this->host,
            'port'          => $this->port,
            'password'      => $this->password,
            'from_to'       => $this->from_to,
            'from_site'     => $this->from_site,
            'active'        => (boolean) $this->active,
            'ssl'           => $this->ssl,
            'created_at'    => $this->when($this->created_at, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'    => $this->when($this->updated_at, $this->updated_at?->format('Y-m-d H:i:s')),
            'deleted_at'    => $this->when($this->deleted_at, $this->deleted_at?->format('Y-m-d H:i:s')),

        ];
    }
}
