@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Menu Makanan</h5>
                <!-- Tombol Pemicu Modal Tambah Menu -->
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    + Tambah Menu
                </button>
            </div>
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $index => $menu)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($menu->path_gambar)
                                        <img src="{{ asset($menu->path_gambar) }}" alt="{{ $menu->nama_menu }}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $menu->nama_menu }}</td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>
                                    <!-- Tombol Hapus (Minimalis form) -->
                                    <form action="{{ route('vendor.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Anda belum memiliki menu!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
            <form action="{{ route('vendor.menu.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Tambah Menu Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label>Nama Menu</label>
                  <input type="text" name="nama_menu" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label>Harga (Rp)</label>
                  <input type="number" name="harga" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label>Gambar Menu (Opsional)</label>
                  <input type="file" name="path_gambar" class="form-control" accept="image/*">
                  <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB.</small>
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan Menu</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
