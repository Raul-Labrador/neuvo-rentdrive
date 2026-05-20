<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IncidentStatusChanged;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class IncidentController extends Controller {
    function index(Request $request): View {
        $query = Incident::with(['reservation', 'car'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $incidents = $query->get();
        $currentFilter = $request->get('status', '');

        return view('admin.incidents.index', compact('incidents', 'currentFilter'));
    }

    function show($id): View {
        $incident = Incident::with(['reservation.car', 'car', 'images'])->findOrFail($id);

        return view('admin.incidents.show', compact('incident'));
    }

    function update(Request $request, $id): RedirectResponse {
        $validated = $request->validate([
            'status'      => 'required|string|in:open,in_review,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $incident = Incident::findOrFail($id);
        $oldStatus = $incident->status;

        $incident->update($validated);

        $newStatus = $incident->status;

        // Enviar email al cliente solo si el estado cambió
        if ($oldStatus !== $newStatus) {
            try {
                $incident->load(['car', 'reservation']);
                Mail::to($incident->reservation->customer_email)->send(
                    new IncidentStatusChanged($incident, $oldStatus, $newStatus)
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send incident status email: ' . $e->getMessage(), [
                    'incident_id' => $incident->id,
                    'old_status'  => $oldStatus,
                    'new_status'  => $newStatus,
                ]);
            }
        }

        return redirect()
            ->route('admin.incidents.show', $incident->id)
            ->with('success', 'Incident updated successfully.');
    }
}

