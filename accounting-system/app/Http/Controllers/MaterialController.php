<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query()->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('category', 'like', "%{$s}%"));
        }

        $materials = $query->paginate(20)->withQueryString();

        $totals = [
            'count'      => Material::count(),
            'low_stock'  => Material::whereNotNull('min_stock')->whereColumn('current_stock', '<=', 'min_stock')->count(),
        ];

        return view('materials.index', compact('materials', 'totals'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'category'      => 'nullable|string|max:255',
            'current_stock' => 'nullable|numeric|min:0',
            'min_stock'     => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string|max:1000',
            'is_active'     => 'boolean',
        ]);

        $data['current_stock'] = $data['current_stock'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        Material::create($data);

        return redirect()->route('materials.index')->with('success', 'مەواد زیادکرا.');
    }

    public function show(Material $material)
    {
        $movements = $material->movements()->with('user')->latest('movement_date')->paginate(20);
        return view('materials.show', compact('material', 'movements'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'unit'      => 'required|string|max:50',
            'category'  => 'nullable|string|max:255',
            'min_stock' => 'nullable|numeric|min:0',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $material->update($data);

        return redirect()->route('materials.show', $material)->with('success', 'زانیاری نوێکرایەوە.');
    }

    public function destroy(Material $material)
    {
        if ($material->movements()->exists()) {
            return back()->with('error', 'ناتوانرێت بسڕدرێتەوە چونکە جووڵەی کڕین/فرۆشتنی هەیە.');
        }
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'سڕایەوە.');
    }
}
