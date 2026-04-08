@extends('layouts.main')

@push('style')
<style>
    .table-kategori tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .badge-custom {
        font-size: 12px;
        padding: 5px 10px;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-format-list-bulleted"></i>
        </span> Data Kategori
    </h3>
</div>

<div class="row">
    {{-- KOLOM KIRI: FORM TAMBAH/EDIT --}}
    @if(request()->has('tambah') || isset($kategori_edit))
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title {{ isset($kategori_edit) ? 'text-warning' : 'text-primary' }}">
                    <i class="mdi {{ isset($kategori_edit) ? 'mdi-pencil' : 'mdi-plus-circle' }}"></i>
                    {{ isset($kategori_edit) ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h4>
                
                @if(isset($kategori_edit))
                    <form action="{{ route('kategori.update', $kategori_edit->idkategori) }}" method="POST">
                        @method('PUT')
                @else
                    <form action="{{ route('kategori.store') }}" method="POST">
                @endif
                    @csrf
                    
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" 
                               class="form-control @error('nama_kategori') is-invalid @enderror" 
                               value="{{ old('nama_kategori', $kategori_edit->nama_kategori ?? '') }}" 
                               placeholder="Contoh: Novel, Biografi, Komik" 
                               required 
                               autofocus>
                        @error('nama_kategori')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-{{ isset($kategori_edit) ? 'warning' : 'primary' }} btn-sm">
                            <i class="mdi mdi-content-save"></i> {{ isset($kategori_edit) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('kategori.index') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- KOLOM KANAN: TABEL DATA --}}
    <div class="col-lg-{{ (request()->has('tambah') || isset($kategori_edit)) ? '8' : '12' }} grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Kategori</h4>
                @if(!request()->has('tambah') && !isset($kategori_edit))
                <div class="text-right mb-3">
                    <a href="{{ route('kategori.index') }}?tambah=true" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Kategori
                    </a>
                </div>
                @endif
                
                <div class="table-responsive">
                <table class="table table-hover table-kategori">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Nama Kategori</th>
                            <th width="100">Jumlah Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategori as $kat)
                            <tr>
                                <td>{{ $kat->idkategori }}</td>
                                <td>{{ $kat->nama_kategori }}</td>
                                <td>
                                    <span class="badge badge-custom bg-gradient-info">
                                        {{ $kat->bukus->count() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('kategori.edit', $kat->idkategori) }}" 
                                        class="btn btn-gradient-warning btn-sm btn-icon-text">
                                        <i class="mdi mdi-pencil btn-icon-prepend"></i> Edit
                                    </a>

                                    <form action="{{ route('kategori.destroy', $kat->idkategori) }}" method="POST" style="display: inline" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-sm btn-icon-text">
                                            <i class="mdi mdi-delete btn-icon-prepend"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
