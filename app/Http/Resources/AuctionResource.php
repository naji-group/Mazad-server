<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
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
            'image_url'=>$this->social?->image_url,
            'social_name'=>$this->social?->name,
            'customer_name'=>$this->customer_name,
            'price'=>$this->price,
            'id' => $this->id,       
            'is_active'=> $this->is_active,

        //   'marketer_id',
        //   'live_video_id',
         
          //'customer_link',        
        ];
    }
}
