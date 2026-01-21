<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraSocialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return parent::toArray($request);
    // }
    public function toArray(Request $request): array
    {         
        return [
            'id' => $this->id,
            'image_url'=>$this->image_url,            
            'name'=>$this->name,
            'is_extra'=>$this->is_extra,                    
        ];
    }
}
