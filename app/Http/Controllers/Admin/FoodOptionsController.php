<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodOption;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FoodOptionsController extends Controller
{
    public function index(): Response
    {
        $foodOptions = FoodOption::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return Inertia::render('admin/FoodOptions', [
            'foodOptions' => $foodOptions,
        ]);
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable'],
        ]);

        FoodOption::query()->create([
            'label' => $validated['label'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) request()->boolean('is_active'),
        ]);

        return back()->with('success', 'Option nourriture ajoutée.');
    }

    public function update(FoodOption $foodOption): RedirectResponse
    {
        $validated = request()->validate([
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable'],
        ]);

        $foodOption->fill([
            'label' => $validated['label'],
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) request()->boolean('is_active'),
        ]);

        $foodOption->save();

        return back()->with('success', 'Option nourriture mise à jour.');
    }

    public function destroy(FoodOption $foodOption): RedirectResponse
    {
        $foodOption->delete();

        return back()->with('success', 'Option nourriture supprimée.');
    }
}
