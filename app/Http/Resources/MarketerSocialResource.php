<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketerSocialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {         
        return [
            'id' => $this->id,       
            'is_active'=> $this->is_active,
           'code' => $this->social?->code,             
          'link'=> $this->link,           
        ];
    }
}
