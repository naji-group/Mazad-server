<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionStSResource extends JsonResource
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
            'marketer_username' => $this->marketer->username,
            'customer_name'=>$this->customer_name,
            'social_name'=>$this->social?->name,
            'image_url'=>$this->social?->image_url,
           
            'price'=>$this->price,
           
            'created_at'  =>$this->created_at,                 
        ];
    }
}
