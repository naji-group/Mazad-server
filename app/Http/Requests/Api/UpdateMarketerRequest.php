<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketerRequest extends FormRequest
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
            'full_name' => 'required|string|max:100',
            'password' => 'nullable|max:100',
            'id'=> 'required|not_in:0|in:'.auth('api_marketers')->user()->id,
 
'local_image'=>'nullable|file'
 
           // 'password' => 'required_without:email',
         //   'email'    => 'required_without_all:username,password|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        ];
    }
    public function messages(): array
{
 
   return[     
     'full_name.required'=>'The full_name is required',
  //   'name.alpha_num'=>'The name format must be alphabet',
    // 'unique.unique'=>'The name is already exist',
  //  'email.required'=>'Email is required',
  //    'email.email'=>'Valid Email  is required',
   //  'email.unique'=>'The Email is already exist',
 
     
  
    ];
    
}
}
