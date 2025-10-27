<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveCreateInstagramRequest extends FormRequest
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
        'agora_url' => 'required|string',
            'instagram_url' => 'required|string',
            'instagram_key' => 'required|string', 
              ];
    }
    public function messages(): array
{
 
   return[     
    // 'id.*'=> __('api_messages.user not found'), 
 'agora_url.*'=>__('api_messages.data empty'),
'instagram_url.*'=>__('api_messages.data empty'),
'instagram_key.*'=>__('api_messages.data empty'),
    ];
    
}
}
