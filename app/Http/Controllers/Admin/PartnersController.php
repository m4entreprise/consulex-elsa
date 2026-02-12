<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PartnersController extends Controller
{
    public function index(): Response
    {
        $partners = Partner::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/Partners', [
            'partners' => $partners,
        ]);
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_featured' => ['nullable'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:4096'],
        ]);

        $logoPath = null;

        if (request()->hasFile('logo')) {
            $logoPath = Storage::disk('public')->putFile('partners', request()->file('logo'));
        }

        Partner::query()->create([
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_featured' => (bool) request()->boolean('is_featured'),
            'logo_path' => $logoPath,
        ]);

        return back()->with('success', 'Partenaire ajouté.');
    }

    public function update(Partner $partner): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_featured' => ['nullable'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:4096'],
        ]);

        if (request()->hasFile('logo')) {
            if ($partner->logo_path) {
                Storage::disk('public')->delete($partner->logo_path);
            }

            $partner->logo_path = Storage::disk('public')->putFile('partners', request()->file('logo'));
        }

        $partner->fill([
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_featured' => (bool) request()->boolean('is_featured'),
        ]);

        $partner->save();

        return back()->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }

        $partner->delete();

        return back()->with('success', 'Partenaire supprimé.');
    }
}
