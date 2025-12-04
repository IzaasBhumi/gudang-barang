@extends('layouts.kai')
@section('page_title', $pageTitle)
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Detail : {{ $produk->nama_produk }}</h4>
            <a href="{{ route('master-data.produk.index') }}" class="text-primary">Kembali</a>
        </div>
        <div class="card-body">
            <x-meta-item label="Nama Produk" value="{{ $produk->nama_produk }}"/>
            <x-meta-item label="Kategori" value="{{ $produk->kategori->nama_kategori }}"/>
            <x-meta-item label="Deskripsi Produk" value="{{ $produk->deskripsi_produk }}"/>
                <div class="mt-2">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-dark btn-sm btn-round" 
                        data-bs-toggle="modal" data-bs-target="#modalFormVarian" id="btnTambahVarian">
                          Tambah Varian
                        </button>
                    </div>
                    {{-- looping semua data variant --}}
                    <div class="row mt-2">
                        <div class="cols-12">
                            <div class="alert alert-info" style="box-shadow: none;">
                                <span>Belum ada varian produk, silahkan tambahkan yang baru</span>
                            </div>
                        </div>
                    </div>
                    {{--end looping semua data variant --}}
                </div>
            </div>
        </div>
<x-produk.form-varian />
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            let modalEl = $('#modalFormVarian');
            let modal = new bootstrap.Modal(modalEl);
            let $form = $('#modalFormVarian form')

            $("#btnTambahVarian").on('click', function (){
              $form[0].reset();
              $form.attr('action');
              $form.find('small.text-danger').text('');
              $('#modalFormVarian .modal-title').text('Tambah Varian Baru');
              modal.show();
            });
            
            $form.submit(function(e){
                let formData = new FormData(this);

                $.ajax({
                    type: $form.attr('method'),
                    url: $form.attr('action'),
                    data: "formData",
                    success: function (response) {
                      alert('test')
                    },
                    error: function (xhr){
                        let errors = xhr.responseJSON.errors;
                        console.log(errors);
                        $form.find('small.text-danger').text('');
                        $.each(errors, function(key, val){
                            $form.find(['name="' + key + '"']).next('small.text-danger').text(val[0]);
                        })
                    }
                });
            })
        });
    </script>
@endpush