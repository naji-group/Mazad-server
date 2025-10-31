<?php

namespace App\Http\Requests\Api\Auction;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuctionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marketer_id'=> 'required|not_in:0|in:'.auth('api_marketers')->user()->id,
            'live_video_id'=> 'required|string',           
            'price'=>'required|decimal:0,5',
            'social_id'=>'required|integer',
            'customer_name'=>'required|string|max:1000',
           // 'customer_link'=>'nullable',
            
        ];
    }
    public function messages(): array
    {
     
       return[     
        // 'id.*'=> __('api_messages.user not found'), 
    //  'marketer_id.*'=>__('api_messages.data empty'),
    // 'live_video_id.*'=>__('api_messages.data empty'),
    // 'price.*'=>__('api_messages.data empty'),
    // 'social_id.*'=>__('api_messages.data empty'),
    // 'customer_name.*'=>__('api_messages.data empty'),
        ];
        
    }
}
