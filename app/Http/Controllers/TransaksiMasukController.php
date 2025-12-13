<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeTransaksiMasukRequest;
use App\Models\KartuStok;
use App\Models\Transaksi;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransaksiMasukController extends Controller
{
    public $pageTitle = 'Transaksi Masuk';
    public $jenisTransaksi = 'pemasukan';
    public function create(){
        $pageTitle = $this->pageTitle;
        return view('transaksi-masuk.create', compact('pageTitle'));
    }

    public function store(storeTransaksiMasukRequest $request){
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
           'total_harga' => array_sum(array_column($items, 'subTotal')),
           'keterangan' => $request->keterangan,
           'petugas' => Auth::user()->name,
           'pengirim' => $request->pengirim,
           'kontak' => $request->kontak,
        ]);

        foreach ($items as $item) {
            $query = explode('-', $item['text']);
            $varian = VarianProduk::where('nomor_sku', $item['nomor_sku'])->first();
            $transaksi->items()->create([
                'transaksi_id' => $transaksi->id,
                'produk' => $query[0],
                'varian' => $query[1],
                'nomor_batch' => $item['nomor_batch'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'sub_total' => $item['subTotal'],
                'nomor_sku' => $item['nomor_sku'],
            ]);
            $varian->increment('stok_varian', $item['qty']);
            KartuStok::create([
                'nomor_transaksi' => $nomorTransaksi,
                'jenis_transaksi' => 'in',
                'nomor_sku' => $item['nomor_sku'],
                'jumlah_masuk' => $item['qty'],
                'stok_akhir' => $varian->stok_varian,
                'petugas' => Auth::user()->name
            ]);
        }

        toast()->success('Transaksi Masuk berhasil ditambahkan');
        return response()->json([
            'success' => true,
            'redirect_url' => route('transaksi-masuk.create')
        ]);
    }
}
