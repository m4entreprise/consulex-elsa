<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodOption;
use App\Models\SpectatorRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        $orderedById = [];

        foreach ($foodOptions as $o) {
            $orderedById[$o->id] = 0;
        }

        $registrations = SpectatorRegistration::query()
            ->select(['food_option_id', 'food_quantities'])
            ->where(function ($query) {
                $query
                    ->whereNotNull('food_option_id')
                    ->orWhereNotNull('food_quantities');
            })
            ->get();

        foreach ($registrations as $r) {
            $quantities = $r->food_quantities;

            if (is_array($quantities)) {
                foreach ($quantities as $id => $qty) {
                    $intId = (int) $id;
                    if (! array_key_exists($intId, $orderedById)) {
                        continue;
                    }

                    $orderedById[$intId] += max(0, (int) $qty);
                }

                continue;
            }

            if (! empty($r->food_option_id) && array_key_exists((int) $r->food_option_id, $orderedById)) {
                $orderedById[(int) $r->food_option_id] += 1;
            }
        }

        $foodOptions = $foodOptions
            ->map(function (FoodOption $o) use ($orderedById) {
                return [
                    ...$o->toArray(),
                    'ordered_quantity' => (int) ($orderedById[$o->id] ?? 0),
                ];
            })
            ->values();

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

        $sortOrder = array_key_exists('sort_order', $validated)
            ? (int) ($validated['sort_order'] ?? 0)
            : ((int) (FoodOption::query()->max('sort_order') ?? 0) + 1);

        FoodOption::query()->create([
            'label' => $validated['label'],
            'sort_order' => $sortOrder,
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

        $fill = [
            'label' => $validated['label'],
            'is_active' => (bool) request()->boolean('is_active'),
        ];

        if (array_key_exists('sort_order', $validated)) {
            $fill['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        }

        $foodOption->fill($fill);

        $foodOption->save();

        return back()->with('success', 'Option nourriture mise à jour.');
    }

    public function reorder(): RedirectResponse
    {
        $validated = request()->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $ids = array_values($validated['ids']);

        $count = FoodOption::query()->whereIn('id', $ids)->count();

        if ($count !== count($ids)) {
            throw ValidationException::withMessages([
                'ids' => 'Liste invalide.',
            ]);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                FoodOption::query()->whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return back()->with('success', 'Ordre mis à jour.');
    }

    public function destroy(FoodOption $foodOption): RedirectResponse
    {
        $foodOption->delete();

        return back()->with('success', 'Option nourriture supprimée.');
    }
}
