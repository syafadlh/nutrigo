<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller {

    public function index(Request $request) {
        $foods = Food::when($request->search, fn($q) => $q->where('name','like',"%{$request->search}%"))
            ->when($request->meal_type, fn($q) => $q->where('meal_type', $request->meal_type))
            ->latest()->paginate(20);
        return view('admin.foods.index', compact('foods'));
    }

    public function create() { return view('admin.foods.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'calories'     => 'required|numeric|min:0',
            'proteins'     => 'nullable|numeric|min:0',
            'fat'          => 'nullable|numeric|min:0',
            'carbohydrate' => 'nullable|numeric|min:0',
            'composition'  => 'nullable|string',
            'origin'       => 'nullable|string|max:100',
            'region'       => 'nullable|string|max:100',
            'meal_type'    => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        Food::create($data);
        return redirect()->route('admin.foods.index')->with('success', 'Makanan berhasil ditambahkan!');
    }

    public function edit(Food $food) { return view('admin.foods.edit', compact('food')); }

    public function update(Request $request, Food $food) {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'calories'     => 'required|numeric|min:0',
            'proteins'     => 'nullable|numeric|min:0',
            'fat'          => 'nullable|numeric|min:0',
            'carbohydrate' => 'nullable|numeric|min:0',
            'composition'  => 'nullable|string',
            'origin'       => 'nullable|string|max:100',
            'region'       => 'nullable|string|max:100',
            'meal_type'    => 'required|in:breakfast,lunch,dinner,snack',
            'is_active'    => 'boolean',
        ]);

        $food->update($data);
        return redirect()->route('admin.foods.index')->with('success', 'Data makanan diperbarui!');
    }

    public function destroy(Food $food) {
        $food->delete();
        return back()->with('success', 'Makanan berhasil dihapus.');
    }
}