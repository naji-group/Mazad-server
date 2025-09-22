<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMarketerRequest extends FormRequest
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
       return[
        'id'=> 'required|not_in:0|in:'.auth('api_marketers')->user()->id,
          //  'email'=>'required|email',  
          //  'mobile'=>'required|between:4,20',  
          //  'reason'=>'nullable|max:1000',  
           
       ];   
    
    }
    /**
 * Get the error messages for the defined validation rules.
 *
 * @return array<string, string>
 */
public function messages(): array
{
  
   return[     
    
     'id.required'=>'id is required',
     
    ];
    
}
}
