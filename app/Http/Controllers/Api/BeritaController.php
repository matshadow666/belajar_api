<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::with('kategori', 'tag', 'user')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Berita',
            'data' => $berita,
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|unique:beritas',
            'deskripsi' => 'required',
            'foto' => 'required|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'tag' => 'required|array',
            'id_user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            // upload foto
            $path = $request->File('foto')->store('public/berita');

            $berita = new Berita;
            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->slug);
            $berita->deskripsi = $request->deskripsi;
            $berita->foto = $path;
            $berita->id_kategori = $request->id_kategori;
            $berita->id_user = $request->id_user;
            $berita->save();

            // Lampiran Banyak Tag
            $berita->tag()->attach($request->tag);
            return response()->json([
                'success' => true,
                'message' => 'Data Berita Berhasil Ditambahkan',
                'data' => $berita,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $berita = Berita::findOrFail($id)->with('kategori', 'tag')->first();
            return response()->json([
                'success' => true,
                'message' => 'Detail Berita',
                'data' => $berita,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita Tidak Ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'deskripsi' => 'required',
            'foto' => 'nullable|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'tag' => 'required|array',
            'id_user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {

            $berita = Berita::findOrFail($id);
            // hpus foto lama
            if ($request->hasFile('foto')) {
                Storage::delete($berita->foto);
                $path = $request->file('foto')->store('public/berita');
                $berita->foto = $path;
            }
            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->slug);
            $berita->deskripsi = $request->deskripsi;
            $berita->id_kategori = $request->id_kategori;
            $berita->id_user = $request->id_user;
            $berita->save();

            // Lampiran Banyak Tag
            $berita->tag()->attach($request->tag);
            return response()->json([
                'success' => true,
                'message' => 'Data Berita Berhasil Ditambahkan',
                'data' => $berita,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    public function destroy(string $id)
    {
        try {
            $berita = Berita::findOrFail($id);
            // hapus tag berita
            $berita->tag()->detach();
            // hapus foto
            Storage::delete($berita->foto);
            $berita->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berita ' . $berita . 'berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Berita Tidak Ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }

    }
}
