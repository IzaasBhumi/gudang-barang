<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeTransaksiKeluarRequest;
use App\Models\KartuStok;
use App\Models\Transaksi;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransaksiKeluarController extends Controller
{
    public $pageTitle = 'Transaksi Keluar';
    public $jenisTransaksi = 'pengeluaran';

    public function create()
    {
        $pageTitle = $this->pageTitle;
        return view('transaksi-keluar.create', compact('pageTitle'));
    }

    public function store(storeTransaksiKeluarRequest $request){
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());
        if($validator -> fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $nomorTransaksi = Transaksi::generateNomorTransaksi($this->jenisTransaksi);
        $items = $request->items;

        $transaksi = Transaksi::create([
           'nomor_transaksi' => $nomorTransaksi,
           'jenis_transaksi' => $this->jenisTransaksi,
           'jumlah_barang' => count($items),
           'total_harga' => 0,
           'keterangan' => $request->keterangan,
           'petugas' => Auth::user()->name,
           'penerima' => $request->penerima,
           'kontak' => $request->kontak,
        ]);

        foreach ($items as $item) {
            $query = explode('-', $item['text']);
            $varian = VarianProduk::where('nomor_sku', $item['nomor_sku'])->first();
            $transaksi->items()->create([
                'transaksi_id' => $transaksi->id,
                'produk' => $query[0],
                'varian' => $query[1],
                'qty' => $item['qty'],
                'harga' => $varian->harga_varian,
                'sub_total' => $varian->harga_varian * $item['qty'],
                'nomor_sku' => $item['nomor_sku'],
            ]);
            $varian->decrement('stok_varian', $item['qty']);
            KartuStok::create([
                'nomor_transaksi' => $nomorTransaksi,
                'jenis_transaksi' => 'out',
                'nomor_sku' => $item['nomor_sku'],
                'jumlah_keluar' => $item['qty'],
                'stok_akhir' => $varian->stok_varian,
                'petugas' => Auth::user()->name
            ]);

            $transaksi->total_harga += $varian->harga_varian * $item['qty'];
            $transaksi->save();
        }

        toast()->success('Transaksi Masuk berhasil ditambahkan');
        return response()->json([
            'success' => true,
            'redirect_url' => route('transaksi-keluar.create')
        ]);
    }
}
