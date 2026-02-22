<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JuryMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class JuryMembersController extends Controller
{
    private function uploadMessages(): array
    {
        return [
            'photo.uploaded' => "La photo n'a pas pu être envoyée (fichier trop lourd ou configuration serveur).",
            'photo.max' => 'La photo est trop lourde (max 10 Mo).',
            'photo.mimes' => 'Format de photo invalide (png, jpg, jpeg, webp, svg).',
        ];
    }

    public function index(): Response
    {
        $juryMembers = JuryMember::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/JuryMembers', [
            'juryMembers' => $juryMembers,
        ]);
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'detail' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable'],
            'photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:10240'],
        ], $this->uploadMessages());

        $photoPath = null;

        if (request()->hasFile('photo')) {
            $photoPath = Storage::disk('public')->putFile('jury', request()->file('photo'));
        }

        $sortOrder = array_key_exists('sort_order', $validated)
            ? (int) ($validated['sort_order'] ?? 0)
            : ((int) (JuryMember::query()->max('sort_order') ?? 0) + 1);

        JuryMember::query()->create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'detail' => $validated['detail'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => $sortOrder,
            'is_active' => (bool) request()->boolean('is_active'),
            'photo_path' => $photoPath,
        ]);

        return back()->with('success', 'Membre du jury ajouté.');
    }

    public function update(JuryMember $juryMember): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'detail' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable'],
            'photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:10240'],
        ], $this->uploadMessages());

        if (request()->hasFile('photo')) {
            if ($juryMember->photo_path) {
                Storage::disk('public')->delete($juryMember->photo_path);
            }

            $juryMember->photo_path = Storage::disk('public')->putFile('jury', request()->file('photo'));
        }

        $fill = [
            'name' => $validated['name'],
            'role' => $validated['role'],
            'detail' => $validated['detail'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) request()->boolean('is_active'),
        ];

        if (array_key_exists('sort_order', $validated)) {
            $fill['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        }

        $juryMember->fill($fill);

        $juryMember->save();

        return back()->with('success', 'Membre du jury mis à jour.');
    }

    public function reorder(): RedirectResponse
    {
        $validated = request()->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $ids = array_values($validated['ids']);

        $count = JuryMember::query()->whereIn('id', $ids)->count();

        if ($count !== count($ids)) {
            throw ValidationException::withMessages([
                'ids' => 'Liste invalide.',
            ]);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                JuryMember::query()->whereKey($id)->update(['sort_order' => $index]);
            }
        });

        return back()->with('success', 'Ordre mis à jour.');
    }

    public function destroy(JuryMember $juryMember): RedirectResponse
    {
        if ($juryMember->photo_path) {
            Storage::disk('public')->delete($juryMember->photo_path);
        }

        $juryMember->delete();

        return back()->with('success', 'Membre du jury supprimé.');
    }
}
