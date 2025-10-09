<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TokenSaveRequest extends FormRequest
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
          
            'id'=> 'required|not_in:0|in:'.auth('api_marketers')->user()->id,
  'access_token'=>'required'
 
              ];
    }
    public function messages(): array
{
 
   return[     
     'id.*'=> __('api_messages.user not found'),
 'access_token.required'=>__('api_messages.data empty')
 
     
  
    ];
    
}
}
