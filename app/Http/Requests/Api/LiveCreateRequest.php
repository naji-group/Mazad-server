<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveCreateRequest extends FormRequest
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
           // 'fbToken'=>'required',
           // 'title'=>'nullable',
          //  'description'=>'nullable',
            'agora_live_id'=>'required',
            'channel' => 'required|string',
            'rtmpUrl' => 'required|string',
         //   'id'=> 'required|not_in:0|in:'.auth('api_marketers')->user()->id,
              ];
    }
    public function messages(): array
{
 
   return[     
    // 'id.*'=> __('api_messages.user not found'),
 'fbToken.*'=>__('api_messages.data empty'),
 'agora_live_id.*'=>__('api_messages.data empty'),
 'rtmpUrl.*'=>__('api_messages.data empty'),
    ];
    
}
}
