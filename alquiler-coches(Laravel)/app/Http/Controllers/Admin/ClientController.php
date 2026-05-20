<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WordPressService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller {
    function index(WordPressService $wpService): View {
        $clients = $wpService->getSubscribers();
        return view('admin.clients.index', compact('clients'));
    }

    function create(): View {
        return view('admin.clients.create');
    }

    function store(Request $request, WordPressService $wpService): RedirectResponse{
        $request->validate([
            'username'   => 'required|string|max:60',
            'email'      => 'required|email|max:100',
            'password'   => 'required|string|min:6',
            'first_name' => 'nullable|string|max:50',
            'last_name'  => 'nullable|string|max:50',
        ]);

        try {
            $wpService->createUser($request->all());
            return redirect()->route('admin.clients.index')->with('success', 'Client created successfully in WordPress.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'API Error: ' . $e->getMessage()]);
        }
    }

    function edit($id, WordPressService $wpService): View {
        $client = $wpService->getUser($id);
        if (!$client) {
            return redirect()->route('admin.clients.index')->with('error', 'Client not found.');
        }
        return view('admin.clients.edit', compact('client'));
    }

    function update(Request $request, $id, WordPressService $wpService): RedirectResponse {
        $request->validate([
            'email'      => 'required|email|max:100',
            'password'   => 'nullable|string|min:6',
            'first_name' => 'nullable|string|max:50',
            'last_name'  => 'nullable|string|max:50',
        ]);

        try {
            $wpService->updateUser($id, $request->all());
            return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully in WordPress.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'API Error: ' . $e->getMessage()]);
        }
    }

    function destroy($id, WordPressService $wpService): RedirectResponse {
        try {
            $wpService->deleteUser($id);
            return redirect()->route('admin.clients.index')->with('success', 'Client permanently deleted from WordPress.');
        } catch (\Exception $e) {
            return redirect()->route('admin.clients.index')->with('error', 'API Error: ' . $e->getMessage());
        }
    }
}
