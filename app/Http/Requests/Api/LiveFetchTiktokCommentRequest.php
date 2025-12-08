<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LiveFetchTiktokCommentRequest extends FormRequest
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
            'author_name' => 'required|string',
            'comment' => 'required|string',
            'userId' => 'nullable|string',
            'commentId' => 'required|string',
            'createtime' => 'required',
            'avatar' => 'nullable|string',
            'livestream_id' => 'required|string',
              ];
    }
    public function messages(): array
{
 
    return[     
        // 'id.*'=> __('api_messages.user not found'),
     'livestream_id.*'=>__('api_messages.data empty'),
     'author_name.*'=>__('api_messages.data empty'),
     'userId.*'=>__('api_messages.data empty'),
     'comment.*'=>__('api_messages.data empty'),
     'commentId.*'=>__('api_messages.data empty'),
     'createtime.*'=>__('api_messages.data empty'),
     'avatar.*'=>__('api_messages.data empty'),
        ];
    
}
}
