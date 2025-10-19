<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveStartRequest extends FormRequest
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
            'youtube_live_chat_id' => 'nullable',
            'youtube_access_token' => 'nullable',
            'facebook_live_video_id' => 'nullable',
            'facebook_access_token' => 'nullable',
 
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
