<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReturnRequest;
use App\Models\Reservation;
use App\Models\ReturnImage;
use App\Models\VehicleReturn;
use Illuminate\Http\JsonResponse;

class ReturnController extends Controller {
    function store(StoreReturnRequest $request): JsonResponse {
        $validated = $request->validated();

        // Verificar que la reserva existe
        $reservation = Reservation::findOrFail($validated['reservation_id']);

        // Crear registro de devolución
        $return = VehicleReturn::create([
            'reservation_id' => $validated['reservation_id'],
            'km_returned'    => $validated['km_returned'],
            'is_clean'       => $validated['is_clean'],
            'notes'          => $validated['notes'] ?? null,
            'damages'        => $validated['damages'] ?? null,
            'returned_at'    => now(),
        ]);

        // Guardar imágenes si se enviaron
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('returns', 'public');

                ReturnImage::create([
                    'vehicle_return_id' => $return->id,
                    'path'             => $path,
                    'type'             => 'general',
                ]);
            }
        }

        // Actualizar estado de la reserva a completed
        $reservation->status = 'completed';
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle returned successfully',
            'data'    => $return,
        ], 201);
    }

    function showByReservation($reservationId): JsonResponse {
        $return = VehicleReturn::with('images')
            ->where('reservation_id', $reservationId)
            ->first();

        if (!$return) {
            return response()->json([
                'success' => false,
                'message' => 'Return not found',
            ], 404);
        }

        $data = [
            'id'             => $return->id,
            'reservation_id' => $return->reservation_id,
            'km_returned'    => $return->km_returned,
            'is_clean'       => $return->is_clean,
            'notes'          => $return->notes,
            'damages'        => $return->damages,
            'returned_at'    => $return->returned_at,
            'images'         => $return->images->map(function ($img) {
                return [
                    'id'   => $img->id,
                    'type' => $img->type,
                    'path' => $img->path,
                    'url'  => asset('storage/' . $img->path),
                ];
            })->values(),
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }
}
