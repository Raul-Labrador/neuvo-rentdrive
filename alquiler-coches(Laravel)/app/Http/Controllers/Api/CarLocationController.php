<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarLocation;
use Illuminate\Support\Facades\Log;

class CarLocationController extends Controller {
    function updateLocation(Request $request) {
        Log::info('Datos recibidos del GPS:', $request->all());

        // Extraemos el ID del coche. La app lo manda en 'device_id'
        $carId = $request->input('device_id', $request->input('id', 1));

        // Vamos a buscar las coordenadas.
        // Primero, intentamos leer el formato complejo de la app que estás usando (location.coords.latitude)
        $lat = $request->input('location.coords.latitude');
        $lng = $request->input('location.coords.longitude');

        // Si no vienen en ese formato complejo, probamos con el formato normal
        if (!$lat || !$lng) {
            $lat = $request->input('lat', $request->input('latitude'));
            $lng = $request->input('lon', $request->input('lng', $request->input('longitude')));
        }

        // Si por fin tenemos latitud y longitud, guardamos en la base de datos
        if ($lat && $lng) {
            // Buscamos si ya existe el coche. Si existe, actualizamos. Si no, lo creamos.
            CarLocation::updateOrCreate(
                ['car_id' => $carId],
                ['lat' => $lat, 'lng' => $lng]
            );
            
            return response()->json(['status' => 'success']);
        }
    }

    // Función para enviar la ubicación al mapa
    function getLatestLocation($carId) {
        // Buscamos la última coordenada de ese coche
        $location = CarLocation::where('car_id', $carId)
                    ->orderBy('updated_at', 'desc')
                    ->first();

        if ($location) {
            return response()->json([
                'status' => 'success',
                'lat' => $location->lat,
                'lng' => $location->lng,
                'updated_at' => $location->updated_at->format('H:i:s')
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'No hay datos'], 404);
    }
}