@extends('layouts.kai')
@section('page_title', $pageTitle)
@section('content')
<div class="card">
    <div class="card-body py-5">
      <div class="row align-items-center">
        {{-- filter --}}
        <div class="row col-10"></div>
        {{-- end filter --}}
          {{-- form --}}
          <div class="col-2 d-flex justify-content-end">
            <x-produk.form-produk />
          </div>
          {{-- end form --}}
      </div>
        <table class="table mt-5">
            <thead>
                <tr>
                    <th class="text-center" style="width: 15px">No</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th class="text-center" style="width: 100px">Opsi</th>
                </tr>
            </thead>
            <tbody>
              @forelse ($produk as $index => $item)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_produk }}</td>
                    <td>{{ $item->kategori->nama_kategori }}</td>
                    <td>
                      <div class="d-flex align-items-center gap-1">
                        <x-confirm-delete id="{{ $item->id }}" route="master-data.produk.destroy"/>
                      </div>
                    </td>
                  </tr>
              @empty
                  <tr>
                    <td colspan="4" class="text-center">Data Produk tidak tersedia</td>
                  </tr>
              @endforelse
            </tbody>
        </table>
        {{ $produk->links() }}
    </div>
</div>
@endsection
