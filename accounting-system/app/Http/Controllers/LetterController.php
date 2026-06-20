<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LetterController extends Controller
{
    public function index(Request $request)
    {
        $query = Letter::query()->latest('letter_date')->latest('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('reference_number', 'like', "%{$s}%")
                ->orWhere('subject', 'like', "%{$s}%")
                ->orWhere('recipient', 'like', "%{$s}%"));
        }

        $letters = $query->paginate(20)->withQueryString();

        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        $letter = new Letter(['letter_date' => now()->toDateString()]);

        return view('letters.create', compact('letter'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLetter($request);
        $data['user_id'] = Auth::id();

        $letter = Letter::create($data);

        return redirect()->route('letters.index')->with('success', 'نووسراو تۆمارکرا.');
    }

    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {
        $letter->update($this->validateLetter($request));

        return redirect()->route('letters.index')->with('success', 'نووسراو نوێکرایەوە.');
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();

        return redirect()->route('letters.index')->with('success', 'نووسراو سڕایەوە.');
    }

    public function print(Letter $letter)
    {
        return view('letters.print', [
            'letter' => $letter,
            'logo'   => $this->logoDataUri(),
        ]);
    }

    private function validateLetter(Request $request): array
    {
        return $request->validate([
            'reference_number' => 'required|string|max:255',
            'letter_date'      => 'required|date',
            'recipient'        => 'nullable|string|max:255',
            'subject'          => 'nullable|string|max:255',
            'body'             => 'nullable|string',
        ]);
    }

    private function logoDataUri(): string
    {
        $path = public_path('images/logo.png');
        if (is_file($path)) {
            return 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
        }

        return '';
    }
}
