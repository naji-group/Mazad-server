<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveEndRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
           
            'agora_live_id' => 'required',
            
 
              ];
    }
    public function messages(): array
{
 
   return[     
    // 'id.*'=> __('api_messages.user not found'),
 'marketer_id.*'=>__('api_messages.data empty'),
 'agora_live_id.*'=>__('api_messages.data empty'),
 
    ];
    
}
}
