<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveFetchTiktokStatisticRequest extends FormRequest
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
        
            'livestream_id' => 'required',
            'type'=>'required|string',
            'count'=>'required',
              ];
    }
    public function messages(): array
{
 
    return[     
        // 'id.*'=> __('api_messages.user not found'),
     'livestream_id.*'=>__('api_messages.data empty'),
 'type.*'=>__('api_messages.data empty'),
 'count.*'=>__('api_messages.data empty'),
        ];
    
}
}
