<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JuryMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable'],
            'photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:10240'],
        ], $this->uploadMessages());

        $photoPath = null;

        if (request()->hasFile('photo')) {
            $photoPath = Storage::disk('public')->putFile('jury', request()->file('photo'));
        }

        JuryMember::query()->create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'detail' => $validated['detail'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
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

        $juryMember->fill([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'detail' => $validated['detail'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) request()->boolean('is_active'),
        ]);

        $juryMember->save();

        return back()->with('success', 'Membre du jury mis à jour.');
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
