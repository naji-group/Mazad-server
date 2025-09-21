<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketerProfileResource extends JsonResource
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
            'full_name' => $this->full_name,             
           'is_active'=> $this->is_active,
           'local_image_url'=> $this->local_image_url,
        ];
    }
}
