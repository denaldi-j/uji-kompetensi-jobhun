<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $buku = Buku::query()
            ->when($searchTerm, function ($query, $search) {
                $query->where('judul', 'like', '%' . $search . '%');
            })
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Buku', compact('buku', 'searchTerm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'stok' => 'required|integer|min:1',
        ]);

        Buku::create($request->all());

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'stok' => 'required|integer|min:1',
        ]);

        $buku->update($request->all());

        return redirect()->back()->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->back()->with('success', 'Buku berhasil dihapus.');
    }
}
