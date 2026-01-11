<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FetchYoutubeCommentRequest extends FormRequest
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
           
            'livestream_id' => 'required|string',
              ];
    }
    public function messages(): array
{
 
    return[     
        // 'id.*'=> __('api_messages.user not found'),
     'livestream_id.*'=>__('api_messages.data empty'),
   
        ];
    
}
}
