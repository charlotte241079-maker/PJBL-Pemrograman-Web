<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class NoteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $notes = Note::with('umkm')->latest()->get();
        } else {
            abort_unless(
                $user->isUmkm() && $user->umkm_id,
                403
            );

            $notes = Note::where('umkm_id', $user->umkm_id)
                ->latest()
                ->get();
        }

        return view('catatan.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        abort_unless(
            $user->isUmkm() && $user->umkm_id,
            403
        );

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $validated['content'] = Purifier::clean(
            $validated['content']
        );

        $user->umkm->notes()->create($validated);

        return redirect()
            ->route('notes.index')
            ->with('success', 'Catatan berhasil disimpan!');
    }

    public function destroy(Note $note)
    {
        $user = auth()->user();

        abort_unless(
            $user->isUmkm() &&
            $user->umkm_id &&
            $note->umkm_id === $user->umkm_id,
            403
        );

        $note->delete();

        return redirect()
            ->route('notes.index')
            ->with('success', 'Catatan berhasil dihapus!');
    }
}
