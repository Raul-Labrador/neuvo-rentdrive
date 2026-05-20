<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\WordPressService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CarController extends Controller {
    function index(): View {
        $cars = Car::all();
        return view('admin.cars.index', compact('cars'));
    }

    function create(): View {
        return view('admin.cars.create');
    }

    function store(Request $request): RedirectResponse {
        $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'required|string',
            'price_per_day'       => 'required|numeric|min:0',
            'brand'               => 'nullable|string|max:100',
            'model'               => 'nullable|string|max:100',
            'year'                => 'nullable|integer|min:1900|max:2099',
            'price'               => 'nullable|numeric|min:0',
            'fuel'                => 'nullable|string|max:50',
            'km'                  => 'nullable|integer|min:0',
            'transmission'        => 'nullable|string|max:50',
            'engine_displacement' => 'nullable|string|max:50',
            'horsepower'          => 'nullable|integer|min:0',
            'emissions'           => 'nullable|string|max:50',
            'doors'               => 'nullable|integer|min:1|max:10',
            'seats'               => 'nullable|integer|min:1|max:20',
            'body'                => 'nullable|string|max:50',
            'trunk'               => 'nullable|string|max:50',
            'color'               => 'nullable|string|max:50',
            'features'            => 'nullable|array',
            'features.*'          => 'nullable|string|max:100',
            'featured_image'      => 'nullable|image|max:2048',
            'gallery_images.*'    => 'nullable|image|max:2048',
        ]);

        $car = Car::create([
            'name'                => $request->name,
            'slug'                => Str::slug($request->name),
            'description'         => $request->description,
            'price_per_day'       => $request->price_per_day,
            'is_active'           => $request->boolean('is_active'),
            'brand'               => $request->brand,
            'model'               => $request->model,
            'year'                => $request->year,
            'price'               => $request->price,
            'fuel'                => $request->fuel,
            'km'                  => $request->km,
            'transmission'        => $request->transmission,
            'engine_displacement' => $request->engine_displacement,
            'horsepower'          => $request->horsepower,
            'emissions'           => $request->emissions,
            'doors'               => $request->doors,
            'seats'               => $request->seats,
            'body'                => $request->body,
            'trunk'               => $request->trunk,
            'color'               => $request->color,
            'features'            => $request->features,
        ]);

        $wpId = app(WordPressService::class)->syncCar($car->toArray());

        if ($wpId) {
            $wpService = app(WordPressService::class);
            if ($request->hasFile('featured_image')) {
                $wpService->uploadMedia($request->file('featured_image'), $wpId, true);
            }
            if ($request->file('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $wpService->uploadMedia($image, $wpId, false);
                }
            }
        }

        return redirect()->route('admin.cars.index')->with('success', 'Coche creado y sincronizado con WordPress');
    }

    function edit(Car $car): View {
        $mediaUrls = [];
        try {
            $media = app(WordPressService::class)->getMediaForCar($car->slug);
            foreach($media as $m) {
                if(isset($m['id']) && isset($m['source_url'])) {
                    $mediaUrls[] = [
                        'id' => $m['id'],
                        'url' => $m['source_url']
                    ];
                }
            }
        } catch(\Exception $e) {}

        return view('admin.cars.edit', compact('car', 'mediaUrls'));
    }

    function update(Request $request, Car $car): RedirectResponse {
        $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'required|string',
            'price_per_day'       => 'required|numeric|min:0',
            'brand'               => 'nullable|string|max:100',
            'model'               => 'nullable|string|max:100',
            'year'                => 'nullable|integer|min:1900|max:2099',
            'price'               => 'nullable|numeric|min:0',
            'fuel'                => 'nullable|string|max:50',
            'km'                  => 'nullable|integer|min:0',
            'transmission'        => 'nullable|string|max:50',
            'engine_displacement' => 'nullable|string|max:50',
            'horsepower'          => 'nullable|integer|min:0',
            'emissions'           => 'nullable|string|max:50',
            'doors'               => 'nullable|integer|min:1|max:10',
            'seats'               => 'nullable|integer|min:1|max:20',
            'body'                => 'nullable|string|max:50',
            'trunk'               => 'nullable|string|max:50',
            'color'               => 'nullable|string|max:50',
            'features'            => 'nullable|array',
            'features.*'          => 'nullable|string|max:100',
            'featured_image'      => 'nullable|image|max:2048',
            'gallery_images.*'    => 'nullable|image|max:2048',
        ]);

        $car->update([
            'name'                => $request->name,
            'slug'                => Str::slug($request->name),
            'description'         => $request->description,
            'price_per_day'       => $request->price_per_day,
            'is_active'           => $request->boolean('is_active'),
            'brand'               => $request->brand,
            'model'               => $request->model,
            'year'                => $request->year,
            'price'               => $request->price,
            'fuel'                => $request->fuel,
            'km'                  => $request->km,
            'transmission'        => $request->transmission,
            'engine_displacement' => $request->engine_displacement,
            'horsepower'          => $request->horsepower,
            'emissions'           => $request->emissions,
            'doors'               => $request->doors,
            'seats'               => $request->seats,
            'body'                => $request->body,
            'trunk'               => $request->trunk,
            'color'               => $request->color,
            'features'            => $request->features,
        ]);

        $wpId = app(WordPressService::class)->syncCar($car->toArray());

        if ($wpId) {
            $wpService = app(WordPressService::class);
            if ($request->hasFile('featured_image')) {
                $wpService->uploadMedia($request->file('featured_image'), $wpId, true);
            }
            if ($request->file('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $wpService->uploadMedia($image, $wpId, false);
                }
            }
        }

        return redirect()->route('admin.cars.index')->with('success', 'Coche actualizado y sincronizado con WordPress');
    }

    function destroy(Car $car): RedirectResponse {
        $slug = $car->slug;
        $car->delete();

        app(WordPressService::class)->deleteCar($slug);

        return redirect()->route('admin.cars.index')->with('success', 'Coche eliminado y desincronizado de WordPress junto a todas sus imágenes');
    }

    function destroyMedia(Car $car, $mediaId) {
        app(WordPressService::class)->deleteMedia($mediaId);
        return redirect()->route('admin.cars.edit', $car)->with('success', 'Imagen eliminada en WordPress correctamemte');
    }
}