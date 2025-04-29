<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $pengguna = Pengguna::query()
            ->when($searchTerm, function ($query, $search) {
                $query->where('nama', 'like', '%' . $search . '%');
            })
            ->paginate(20)
            ->withQueryString();
            
        return inertia('Pengguna', compact('pengguna', 'searchTerm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_pengguna' => 'required',
            'alamat' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
        ]);

        Pengguna::create($request->all());

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_pengguna' => 'required|in_array:mahasiswa,dosen',
            'alamat' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15',
        ]);

        $pengguna->update($request->all());

        return redirect()->back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
