<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckAvailabilityRequest extends FormRequest {
    function authorize(): bool {
        return true;
    }

    function rules(): array {
        return [
            'car_id'     => 'required|integer|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
        ];
    }

    function messages(): array {
        return [
            'car_id.exists'             => 'El coche seleccionado no existe.',
            'start_date.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior.',
            'end_date.after'            => 'La fecha de fin debe ser posterior a la de inicio.',
        ];
    }

    protected function failedValidation(Validator $validator): void {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
