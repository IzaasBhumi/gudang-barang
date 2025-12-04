<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeVarianProdukRequest;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VarianProdukController extends Controller
{
    public function store(storeVarianProdukRequest $request)
    {
        $fileName = time() . '.' . $request->file('gambar_varian')->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('varian-produk', $request->file('gambar_varian'), $fileName);
        VarianProduk::create([
            'produk_id' => $request->produk_id,
            'nomor_sku' => VarianProduk::generateNomorSKU(),
            'nama_varian' => $request->nama_varian,
            'harga_varian' => $request->harga_varian,
            'stok_varian' => $request->stok_varian,
            'gambar_varian' => $fileName
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan'
        ]);
    }
}
