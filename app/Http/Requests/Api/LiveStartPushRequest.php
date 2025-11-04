<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveStartPushRequest extends FormRequest
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
            'channelName' => 'required|string',
            'uid' => 'required',
            'youtubeStreamKey' => 'required|string',
            'agora_live_id'=>'required',
            
              ];
    }
    public function messages(): array
{
 
   return[     
    // 'id.*'=> __('api_messages.user not found'),
 'channelName.*'=>__('api_messages.data empty'),
 'uid.*'=>__('api_messages.data empty'),
 'youtubeStreamKey.*'=>__('api_messages.data empty'),
 'agora_live_id.*'=>__('api_messages.data empty'),
    ];
    
}
}
