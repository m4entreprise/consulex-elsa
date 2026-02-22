<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticalModality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PracticalModalitiesController extends Controller
{
    public function index(): Response
    {
        $modalities = PracticalModality::query()
            ->orderBy('order')
            ->get();

        return Inertia::render('admin/PracticalModalities', [
            'modalities' => $modalities,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'icon_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = PracticalModality::max('order') ?? -1;

        PracticalModality::create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Modalité pratique ajoutée.');
    }

    public function update(Request $request, PracticalModality $practicalModality): RedirectResponse
    {
        $validated = $request->validate([
            'icon_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $practicalModality->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Modalité pratique mise à jour.');
    }

    public function destroy(PracticalModality $practicalModality): RedirectResponse
    {
        $practicalModality->delete();

        return redirect()
            ->back()
            ->with('success', 'Modalité pratique supprimée.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:practical_modalities,id',
        ]);

        foreach ($validated['items'] as $index => $item) {
            PracticalModality::where('id', $item['id'])->update(['order' => $index]);
        }

        return redirect()
            ->back()
            ->with('success', 'Ordre mis à jour.');
    }
}
