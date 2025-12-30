<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LiveStreamsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        \Log::info('analy',   $this->collection->toArray());
        return [

            // 'id' => $this->id,
            // 'marketer_id' => $this->marketer_id,
            // 'marketer_username' => $this->marketer->username,
            // 'is_active' => $this->is_active,
            // 'status ' => ($this->is_active == 1) ? 'الان' : 'منتهي',
            // // 'duration',
            // 'duration_str' => $this->duration_str,
            // 'start_date' => $this->start_date,
            // 'end_date' => $this->end_date,


        ];
    }
}
