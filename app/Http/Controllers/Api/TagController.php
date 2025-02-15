<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tag = Tag::latest()->get();
        $res = [
            'success' => true,
            'message' => 'Daftar Kategori',
            'data' => $tag,
        ];
        return response()->json($res, 200);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required|unique:tags',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $tag = new Tag();
            $tag->nama_tag = $request->nama_tag;
            $tag->slug = Str::slug($request->nama_tag);
            $tag->save();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil dibuat',
                'data' => $tag,
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
            $tag = Tag::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail Tag',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }

    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_tag' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $tag = Tag::findOrFail($id);
            $tag->nama_tag = $request->nama_tag;
            $tag->slug = Str::slug($request->nama_tag);
            $tag->save();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil diubah',
                'data' => $tag,
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
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data ' . $tag->nama_tag . ' Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }

    }
}

