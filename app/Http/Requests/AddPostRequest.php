<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddPostRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content'         => 'required',
            'price'           => 'required|numeric',
            'status'          => 'nullable',
            'rejected_reason' => 'nullable',
            'worker_id'       => 'nullable'
        ];
    }

    public function requestFailsReturn($validator, $type = 'all_in_string'): \Illuminate\Http\JsonResponse
    {
        switch ($type) {
            case 'all_in_string':
                $msg = implode(',', $validator->errors()->all());
                break;
            case 'first':
                $msg = $validator->errors()->first();
                break;
            case 'all_in_array':
                $msg = $validator->errors()->all();
                break;
            default:
                $msg = 'حدث خطأ ما';
        }
        return $this->failMsg($msg, 401);

    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException($this->requestFailsReturn($validator));
    }

    public function failMsg($msg = '', $code = 401): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'user_status' =>  auth('worker')->user() ? auth('worker')->user()->status : '',
            'key' => 'fail',
            'msg' => $msg,
            'code' => $code,
        ]);
    }
}
