<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIncidentRequest extends FormRequest {
    function authorize(): bool {
        return true;
    }

    function rules(): array {
        return [
            'reservation_id' => 'required|integer|exists:reservations,id',
            'type'           => 'required|string|in:accident,mechanical,damage,warning_light,cleanliness,papers,keys,other',
            'description'    => 'required|string|max:2000',
            'wp_user_id'     => 'required|integer',
            'images'         => 'nullable|array|max:5',
            'images.*'       => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
