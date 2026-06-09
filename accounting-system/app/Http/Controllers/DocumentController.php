<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query()->latest('doc_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('recipient', 'like', "%{$s}%")
                ->orWhere('doc_type', 'like', "%{$s}%"));
        }

        $documents = $query->paginate(20)->withQueryString();

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'doc_type'  => 'nullable|string|max:255',
            'recipient' => 'nullable|string|max:255',
            'body'      => 'nullable|string',
            'doc_date'  => 'required|date',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $data['user_id'] = Auth::id();
        $document = Document::create($data);

        return redirect()->route('documents.show', $document)->with('success', 'نووسراو تۆمارکرا.');
    }

    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'doc_type'  => 'nullable|string|max:255',
            'recipient' => 'nullable|string|max:255',
            'body'      => 'nullable|string',
            'doc_date'  => 'required|date',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $document->update($data);

        return redirect()->route('documents.show', $document)->with('success', 'نووسراو نوێکرایەوە.');
    }

    public function print(Document $document)
    {
        return view('documents.print', compact('document'));
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'سڕایەوە.');
    }
}
