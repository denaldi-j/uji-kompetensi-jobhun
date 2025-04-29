<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $peminjaman = Peminjaman::query()
            ->when($searchTerm, function ($query, $search) {
                $query->where('judul', 'like', '%' . $search . '%');
            })
            ->with(['pengguna', 'buku', 'pengembalian'])
            ->paginate(20)
            ->withQueryString();

        $pengguna = Pengguna::query()
            ->select('id', 'nama')
            ->orderBy('nama', 'asc')
            ->get();
        
        $buku = Buku::query()
            ->select('id', 'judul')
            ->orderBy('judul', 'asc')
            ->get();

        return Inertia::render('Peminjaman', compact('peminjaman','pengguna', 'searchTerm', 'buku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'pengguna_id' => 'required|exists:pengguna,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        Peminjaman::create([
            'buku_id' => $request->buku_id,
            'pengguna_id' => $request->pengguna_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'dipinjam',
        ]);

        return redirect()->back()->with('success', 'Peminjaman berhasil.');
    }

    public function pengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'tanggal_pengembalian' => 'required|date',
            'denda' => 'nullable|integer',
        ]);

        $pengembalian = Pengembalian::updateOrCreate([
            'peminjaman_id' => $request->peminjaman_id,
        ], [
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'denda' => $request->denda,
        ]);

        $pengembalian->peminjaman()->update([
            'status' => 'dikembalikan',
        ]);

        return redirect()->back()->with('success', 'Pengembalian berhasil.');
    }
}
