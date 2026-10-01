<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    // Menampilkan daftar makanan untuk admin
    public function index()
    {
        $food = Food::latest()->paginate(10);
        return view('admin.foods.index', compact('food'));
    }

    // Menampilkan form tambah menu
    public function create()
    {
        return view('admin.foods.create');
    }

    // Simpan menu baru ke database
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:Makanan,Minuman,Cemilan',
            'price'       => 'required|numeric|min:0',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload gambar jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        // Simpan data menu
        Food::create([
            'name'        => $request->name,
            'category'    => $request->category,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil ditambahkan!');
    }

    public function show(Food $food)
    {
        //
    }

    // Menampilkan form edit menu
    public function edit(Food $food)
    {
        return view('admin.foods.edit', compact('food'));
    }

    // Simpan perubahan menu ke database
    public function update(Request $request, Food $food)
    {
        // Validasi data input
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:Makanan,Minuman,Cemilan',
            'price'       => 'required|numeric|min:0',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $food->image;

        // Jika upload gambar baru, hapus file gambar lama
        if ($request->hasFile('image')) {
            if ($food->image && Storage::disk('public')->exists($food->image)) {
                Storage::disk('public')->delete($food->image);
            }
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        // Perbarui data menu
        $food->update([
            'name'        => $request->name,
            'category'    => $request->category,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil diperbarui!');
    }

    // Hapus menu beserta file gambarnya
    public function destroy(Food $food)
    {
        // Hapus file gambar jika ada
        if ($food->image && Storage::disk('public')->exists($food->image)) {
            Storage::disk('public')->delete($food->image);
        }

        // Hapus data dari database
        $food->delete();

        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil dihapus!');
    }
}
