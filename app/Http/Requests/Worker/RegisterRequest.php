<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name'     => 'required|string|between:2,100',
            'email'    => 'required|string|email|max:100|unique:workers',
            'password' => 'required|string|max:16',
            'phone'    => 'required|numeric',
            'photo'    => 'nullable|image|mimes:png,jpg,jpeg',
            'location' => 'required',
        ];
    }
}
