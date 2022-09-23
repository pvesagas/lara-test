<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_no' => 'numeric',
            'name'        => 'string',
            'description' => 'string',
            'price'       => 'numeric',
            'image_path'  => 'nullable',
        ];
    }

    /**
     * @param Validator $validator
     * @return JsonResponse
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'result' => false,
                    'error' => $validator->getMessageBag(),
                ],
                400)
        );
    }
}
