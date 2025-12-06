<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveStartTiktokRequest extends FormRequest
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
            'channel' => 'required|string',
            'rtmpUrl' => 'required|string',
            'agora_live_id'=>'required',
            'uid' => 'nullable',

              ];
    }
    public function messages(): array
{
 
   return[     
    // 'id.*'=> __('api_messages.user not found'),
 'channel.*'=>__('api_messages.data empty'),
 'uid.*'=>__('api_messages.data empty'),
 'rtmpUrl.*'=>__('api_messages.data empty'),
 'agora_live_id.*'=>__('api_messages.data empty')
    ];
    
}
}
