<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AfterMovie;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AfterMoviesController extends Controller
{
    public function index(): Response
    {
        $afterMovies = AfterMovie::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('admin/AfterMovies', [
            'afterMovies' => $afterMovies,
        ]);
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'theme' => ['required', 'string', 'max:255'],
            'winner' => ['required', 'string', 'max:255'],
            'aftermovie_url' => ['nullable', 'url', 'max:255'],
        ]);

        AfterMovie::query()->create($validated);

        return back()->with('success', 'After movie ajouté.');
    }

    public function update(AfterMovie $afterMovie): RedirectResponse
    {
        $validated = request()->validate([
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'theme' => ['required', 'string', 'max:255'],
            'winner' => ['required', 'string', 'max:255'],
            'aftermovie_url' => ['nullable', 'url', 'max:255'],
        ]);

        $afterMovie->fill($validated);
        $afterMovie->save();

        return back()->with('success', 'After movie mis à jour.');
    }

    public function destroy(AfterMovie $afterMovie): RedirectResponse
    {
        $afterMovie->delete();

        return back()->with('success', 'After movie supprimé.');
    }
}
