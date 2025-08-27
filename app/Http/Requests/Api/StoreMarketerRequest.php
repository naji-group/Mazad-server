<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketerRequest extends FormRequest
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
            'username'=>'required|unique:marketers,username', 
            'email'=>'required|email',
        ];
    }
    public function messages(): array
{
 
   return[     
     'username.required'=>'The name is required',
  //   'name.alpha_num'=>'The name format must be alphabet',
     'unique.unique'=>'The name is already exist',
   'email.required'=>'Email is required',
     'email.email'=>'Valid Email  is required',
   //  'email.unique'=>'The Email is already exist',
 
     
  
    ];
    
}
}
