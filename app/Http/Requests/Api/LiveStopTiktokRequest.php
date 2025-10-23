<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveStopTiktokRequest extends FormRequest
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
            'resourceId' => 'required|string',
            'sid' => 'required|string',
            'uid' => 'nullable|string',
 
              ];
    }
    public function messages(): array
{
 
   return[     
    // 'id.*'=> __('api_messages.user not found'),
 'channel.*'=>__('api_messages.data empty'),
 'resourceId.*'=>__('api_messages.data empty'),
 'sid.*'=>__('api_messages.data empty'),
 'uid.*'=>__('api_messages.data empty'),

    ];
    
}
}
