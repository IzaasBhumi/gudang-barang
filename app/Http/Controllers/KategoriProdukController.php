<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeKategoriProdukRequest;
use Illuminate\Http\Request;
use App\Models\KategoriProduk;

class KategoriProdukController extends Controller
{
    public $pageTitle = 'Kategori Produk';
    public function index()
    {
        $pageTitle = $this->pageTitle;
        $query = KategoriProduk::query();
        $kategori = $query->paginate(10);
        return view('kategori-produk.index', compact('pageTitle', 'kategori'));
    }
    public function store(storeKategoriProdukRequest $request){
        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori
        ]);
        toast()->success('Kategori Produk berhasil ditambahkan');
        return redirect()->route('master-data.kategori-produk.index');
    }
}
