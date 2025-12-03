<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeProdukRequest;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public $pageTitle = 'Data Produk';
    public function index(){
        $query = Produk::query();
        $perPage = request()->query('perPage') ?? 10;
        $search = request()->query('search');
        $pageTitle = $this->pageTitle;

        $query->with('kategori:id,nama_kategori');

        if ($search) {
            $query->where('nama_produk', 'like', '%' . $search . '%');
        }

        $produk = $query->orderBy('created_at', 'DESC')
        ->paginate($perPage)->appends(request()->query());
        confirmDelete('Hapus data produk akan menghapus semua varian yang ada, lanjutkan ?');

        return view('produk.index', compact('pageTitle', 'produk'));
    }
    public function store(storeProdukRequest $request){
        Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'kategori_produk_id' => $request->kategori_produk_id
        ]);
        toast()->success('Produk berhasil ditambahkan');
        return redirect()->route('master-data.produk.index');
    }
    public function destroy(Produk $produk)
    {
        $produk->delete();
        toast()->success('Produk berhasil dihapus');
        return redirect()->route('master-data.produk.index');
    }
}
