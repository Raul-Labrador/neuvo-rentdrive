<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest {
    function authorize(): bool {
        return true;
    }

    function rules(): array {
        return [
            'reservation_id' => 'required|integer|exists:reservations,id|unique:vehicle_returns,reservation_id',
            'km_returned'    => 'required|integer|min:0',
            'is_clean'       => 'required|boolean',
            'notes'          => 'nullable|string|max:2000',
            'damages'        => 'nullable|string|max:2000',
            'images'         => 'nullable|array|max:10',
            'images.*'       => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
