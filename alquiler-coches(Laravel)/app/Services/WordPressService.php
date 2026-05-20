<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WordPressService {
    protected string $baseUrl;
    protected string $user;
    protected string $password;

    function __construct() {
        $this->baseUrl  = config('services.wordpress.url');
        $this->user     = config('services.wordpress.user');
        $this->password = config('services.wordpress.password');
    }

    /**
     * Sincroniza un coche de Laravel con WordPress.
     * Si ya existe (por slug), lo actualiza; si no, lo crea.
     */
    function syncCar(array $car): int {
        $existingId = $this->findPostBySlug($car['slug']);

        $payload = [
            'title'   => $car['name'],
            'slug'    => $car['slug'],
            'status'  => ($car['is_active'] ?? true) ? 'publish' : 'draft',
            'content' => $car['description'] ?? '',

            // Campos meta del CPT de WordPress
            'rlp_car_brand'        => $car['brand'] ?? '',
            'rlp_car_model'        => $car['model'] ?? '',
            'rlp_car_year'         => $car['year'] ?? '',
            'rlp_car_price'        => $car['price'] ?? '',
            'rlp_car_fuel'         => $car['fuel'] ?? '',
            'rlp_car_km'           => $car['km'] ?? '',
            'rlp_car_transmission' => $car['transmission'] ?? '',
            'rlp_car_ed'           => $car['engine_displacement'] ?? '',
            'rlp_car_horsepower'   => $car['horsepower'] ?? '',
            'rlp_car_emissions'    => $car['emissions'] ?? '',
            'rlp_car_doors'        => $car['doors'] ?? '',
            'rlp_car_seats'        => $car['seats'] ?? '',
            'rlp_car_body'         => $car['body'] ?? '',
            'rlp_car_trunk'        => $car['trunk'] ?? '',
            'rlp_car_color'        => $car['color'] ?? '',
            'rlp_services'         => array_map(fn($f) => ['service' => $f], array_filter($car['features'] ?? [])),
        ];

        if ($existingId) {
            $this->updatePost($existingId, $payload);
            return $existingId;
        } else {
            return $this->createPost($payload);
        }
    }

    /**
     * Sube una imagen nativa al post de wp como destacada o como galeria
     */
    function uploadMedia($file, int $postId, bool $isFeatured = false): void {
        $filename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $fileContent = file_get_contents($file->getRealPath());

        $response = Http::withBasicAuth($this->user, $this->password)
            ->withHeaders([
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type'        => $mimeType,
            ])
            ->send('POST', "{$this->baseUrl}/wp-json/wp/v2/media", [
                'body' => $fileContent,
            ]);

        if ($response->successful()) {
            $mediaId = $response->json('id');
            // Vincular la imagen al parent post (necesario para galerías y queries WP)
            Http::withBasicAuth($this->user, $this->password)
                ->post("{$this->baseUrl}/wp-json/wp/v2/media/{$mediaId}", [
                    'post' => $postId,
                ]);

            // Si es destacada también marcamos el featured_media
            if ($isFeatured) {
                Http::withBasicAuth($this->user, $this->password)
                    ->post("{$this->baseUrl}/wp-json/wp/v2/cars/{$postId}", [
                        'featured_media' => $mediaId,
                    ]);
            }
        }
    }

    /**
     * Obtiene los medios (imágenes) asociadas a un vehículo.
     */
    function getMediaForCar(string $slug): array {
        $existingId = $this->findPostBySlug($slug);
        if (!$existingId) return [];

        $response = Http::withBasicAuth($this->user, $this->password)
            ->get("{$this->baseUrl}/wp-json/wp/v2/media", [
                'parent' => $existingId
            ]);
        
        return $response->json() ?? [];
    }

    /**
     * Elimina una imagen en WordPress.
     */
    function deleteMedia(int $mediaId): void {
        Http::withBasicAuth($this->user, $this->password)
            ->delete("{$this->baseUrl}/wp-json/wp/v2/media/{$mediaId}", [
                'force' => true
            ]);
    }

    /**
     * Elimina un CPT en WordPress por su slug, primero borrando sus imágenes.
     */
    function deleteCar(string $slug): void {
        $existingId = $this->findPostBySlug($slug);

        if ($existingId) {
            // Eliminar todas las imagenes primero
            $media = $this->getMediaForCar($slug);
            foreach ($media as $item) {
                if (isset($item['id'])) {
                    $this->deleteMedia($item['id']);
                }
            }

            // Eliminar post
            $response = Http::withBasicAuth($this->user, $this->password)
                ->delete("{$this->baseUrl}/wp-json/wp/v2/cars/{$existingId}", [
                    'force' => true,
                ]);

            Log::info('WP deleteCar response', [
                'wp_id'  => $existingId,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        }
    }

    private function findPostBySlug(string $slug): ?int {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->get("{$this->baseUrl}/wp-json/wp/v2/cars", [
                'slug'   => $slug,
                'status' => 'any',
            ]);

        $posts = $response->json();
        return !empty($posts) ? $posts[0]['id'] : null;
    }

    private function createPost(array $payload): int {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->post("{$this->baseUrl}/wp-json/wp/v2/cars", $payload);

        return current((array) $response->json('id')) ?? 0;
    }

    private function updatePost(int $wpId, array $payload): void {
        Http::withBasicAuth($this->user, $this->password)
            ->post("{$this->baseUrl}/wp-json/wp/v2/cars/{$wpId}", $payload);
    }

    /**
     * Obtiene el número total de usuarios con el rol 'subscriber' en WordPress.
     */
    function getSubscriberCount(): int {
        try {
            $response = Http::withBasicAuth($this->user, $this->password)
                ->get("{$this->baseUrl}/wp-json/wp/v2/users", [
                    'roles'    => 'subscriber',
                    'per_page' => 1,
                ]);

            return (int) ($response->header('X-WP-Total') ?? 0);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching WP subscriber count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene todos los usuarios con el rol 'subscriber' de WordPress.
     */
    function getSubscribers(): array {
        try {
            $response = Http::withBasicAuth($this->user, $this->password)
                ->get("{$this->baseUrl}/wp-json/wp/v2/users", [
                    'roles'    => 'subscriber',
                    'per_page' => 100, // Ajustar si hay muchos
                    'context'  => 'edit',
                ]);

            if ($response->successful()) {
                return $response->json();
            }
            return [];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching WP subscribers: ' . $e->getMessage());
            return [];
        }
    }

    function getUser(int $id): ?array {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->get("{$this->baseUrl}/wp-json/wp/v2/users/{$id}", [
                'context' => 'edit',
            ]);
            
        return $response->successful() ? $response->json() : null;
    }

    function createUser(array $data): int {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->post("{$this->baseUrl}/wp-json/wp/v2/users", [
                'username'   => $data['username'],
                'name'       => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                'first_name' => $data['first_name'] ?? '',
                'last_name'  => $data['last_name'] ?? '',
                'email'      => $data['email'],
                'password'   => $data['password'],
                'roles'      => ['subscriber'],
            ]);
            
        $response->throw();
        return current((array) $response->json('id')) ?? 0;
    }

    function updateUser(int $id, array $data): void {
        $payload = [
            'name'       => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            'first_name' => $data['first_name'] ?? '',
            'last_name'  => $data['last_name'] ?? '',
            'email'      => $data['email'],
        ];
        
        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        Http::withBasicAuth($this->user, $this->password)
            ->post("{$this->baseUrl}/wp-json/wp/v2/users/{$id}", $payload)
            ->throw();
    }

    function deleteUser(int $id): void {
        Http::withBasicAuth($this->user, $this->password)
            ->delete("{$this->baseUrl}/wp-json/wp/v2/users/{$id}", [
                'force' => true,
                'reassign' => 1 // reassign to admin usually
            ])->throw();
    }
}