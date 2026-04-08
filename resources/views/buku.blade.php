@extends('layouts.main')

@push('style')
<style>
    .table-buku tbody tr:hover {
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
            <i class="mdi mdi-book-open-variant"></i>
        </span> Data Buku
    </h3>
</div>

<div class="row">
    {{-- KOLOM KIRI: FORM TAMBAH/EDIT --}}
    @if(request()->has('tambah') || isset($buku_edit))
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title {{ isset($buku_edit) ? 'text-warning' : 'text-primary' }}">
                    <i class="mdi {{ isset($buku_edit) ? 'mdi-pencil' : 'mdi-plus-circle' }}"></i>
                    {{ isset($buku_edit) ? 'Edit Buku' : 'Tambah Buku Baru' }}
                </h4>
                
                @if(isset($buku_edit))
                    <form action="{{ route('buku.update', $buku_edit->idbuku) }}" method="POST">
                        @method('PUT')
                @else
                    <form action="{{ route('buku.store') }}" method="POST">
                @endif
                    @csrf
                    
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="idkategori" class="form-control @error('idkategori') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->idkategori }}" 
                                    {{ old('idkategori', $buku_edit->idkategori ?? '') == $kat->idkategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('idkategori')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" name="kode" 
                               class="form-control @error('kode') is-invalid @enderror" 
                               value="{{ old('kode', $buku_edit->kode ?? '') }}" 
                               placeholder="Contoh: NV-01, BO-01" 
                               required>
                        @error('kode')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" name="judul" 
                               class="form-control @error('judul') is-invalid @enderror" 
                               value="{{ old('judul', $buku_edit->judul ?? '') }}" 
                               placeholder="Contoh: Home Sweet Loan" 
                               required>
                        @error('judul')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pengarang <span class="text-danger">*</span></label>
                        <input type="text" name="pengarang" 
                               class="form-control @error('pengarang') is-invalid @enderror" 
                               value="{{ old('pengarang', $buku_edit->pengarang ?? '') }}" 
                               placeholder="Contoh: Almira Bastari" 
                               required>
                        @error('pengarang')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-{{ isset($buku_edit) ? 'warning' : 'primary' }} btn-sm">
                            <i class="mdi mdi-content-save"></i> {{ isset($buku_edit) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('buku.index') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- KOLOM KANAN: TABEL DATA --}}
    <div class="col-lg-{{ (request()->has('tambah') || isset($buku_edit)) ? '8' : '12' }} grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Buku</h4>
                @if(!request()->has('tambah') && !isset($buku_edit))
                <div class="text-right mb-3">
                    <a href="{{ route('buku.index') }}?tambah=true" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Buku
                    </a>
                </div>
                @endif
                
                <div class="table-responsive">
                <table class="table table-hover table-buku">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="100">Kode</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Kategori</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($buku as $b)
                            <tr>
                                <td>{{ $b->idbuku }}</td>
                                <td><span class="badge badge-custom bg-gradient-info">{{ $b->kode }}</span></td>
                                <td>{{ $b->judul }}</td>
                                <td>{{ $b->pengarang }}</td>
                                <td>
                                    <span class="badge badge-custom bg-gradient-success">
                                        {{ $b->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('buku.edit', $b->idbuku) }}" 
                                        class="btn btn-gradient-warning btn-sm btn-icon-text">
                                        <i class="mdi mdi-pencil btn-icon-prepend"></i> Edit
                                    </a>

                                    <form action="{{ route('buku.destroy', $b->idbuku) }}" method="POST" style="display: inline" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
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
                                <td colspan="6" class="text-center">Tidak ada data buku.</td>
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
