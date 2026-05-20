<?php
//
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Mail\ReservationCancelled;
use App\Mail\ReservationConfirmed;
use App\Models\Car;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller {
    protected AvailabilityService $availabilityService;

    function __construct(AvailabilityService $availabilityService) {
        $this->availabilityService = $availabilityService;
    }

    function store(StoreReservationRequest $request): JsonResponse {
        $validated = $request->validated();

        // Comprobar disponibilidad usando el servicio centralizado
        if (!$this->availabilityService->isCarAvailable(
            $validated['car_id'],
            $validated['start_date'],
            $validated['end_date']
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Car no longer available for selected dates',
            ], 409);
        }

        // Calcular precio desde backend
        $car   = Car::select('id', 'name', 'price_per_day')->findOrFail($validated['car_id']);
        $days  = $this->availabilityService->calculateDays($validated['start_date'], $validated['end_date']);
        $totalPrice = (float) number_format($days * $car->price_per_day, 2, '.', '');
        $validated['total_price'] = $totalPrice;

        $reservation = Reservation::create($validated);

        // Enviar email de confirmación
        try {
            Mail::to($reservation->customer_email)->send(
                new ReservationConfirmed($reservation, $car->name, $days)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send reservation email: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva confirmada.',
            'reservation_id' => $reservation->id,
        ], 201);
    }

    function getUserReservations(Request $request): JsonResponse {
        $wpUserId = $request->query('wp_user_id');

        if (empty($wpUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'El parámetro wp_user_id es obligatorio.',
            ], 400);
        }

        $reservations = Reservation::with(['car:id,name'])
            ->where('wp_user_id', (int) $wpUserId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $reservations,
        ], 200);
    }

    function cancel($id, Request $request): JsonResponse {
        $reservation = Reservation::findOrFail($id);

        // Solo el propietario puede cancelar
        if ($reservation->wp_user_id !== (int) $request->input('wp_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Solo se puede cancelar si está confirmed
        if ($reservation->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Reservation cannot be cancelled',
            ], 400);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        // Enviar email de cancelación
        try {
            $reservation->load('car');
            Mail::to($reservation->customer_email)->send(
                new ReservationCancelled($reservation)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send cancellation email: ' . $e->getMessage(), [
                'reservation_id' => $reservation->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled successfully',
        ], 200);
    }
}
