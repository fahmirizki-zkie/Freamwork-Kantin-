@extends('layouts.main')

@push('style')
<style>
    .table-barang tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-tag-multiple"></i>
        </span> Tag Harga Barang
    </h3>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    {{-- KOLOM KIRI: FORM TAMBAH/EDIT --}}
    @if(request()->has('tambah') || isset($barang_edit))
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title {{ isset($barang_edit) ? 'text-warning' : 'text-primary' }}">
                    <i class="mdi {{ isset($barang_edit) ? 'mdi-pencil' : 'mdi-plus-circle' }}"></i>
                    {{ isset($barang_edit) ? 'Edit Barang' : 'Tambah Barang Baru' }}
                </h4>

                @if(isset($barang_edit))
                    <form id="form-barang" action="{{ route('barang.update', $barang_edit->id_barang) }}" method="POST">
                        @method('PUT')
                @else
                    <form id="form-barang" action="{{ route('barang.store') }}" method="POST">
                @endif
                    @csrf

                    @if(isset($barang_edit))
                    <div class="form-group">
                        <label>ID Barang</label>
                        <input type="text" class="form-control" value="{{ $barang_edit->id_barang }}" disabled>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $barang_edit->nama ?? '') }}"
                               placeholder="Nama barang"
                               required>
                        @error('nama') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga <span class="text-danger">*</span></label>
                        <input type="number" name="harga"
                               class="form-control @error('harga') is-invalid @enderror"
                               value="{{ old('harga', $barang_edit->harga ?? '') }}"
                               placeholder="0" min="0"
                               required>
                        @error('harga') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mt-3">
                            <button type="button"
                                id="btnSimpan"
                                onclick="handleSubmit()"
                                class="btn btn-{{ isset($barang_edit) ? 'warning' : 'primary' }} btn-sm">
                                <i class="mdi mdi-content-save"></i>
                                {{ isset($barang_edit) ? 'Update' : 'Simpan' }}
                            </button>
                        <a href="{{ route('barang.index') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- KOLOM KANAN: TABEL DATA --}}
    <div class="col-lg-{{ (request()->has('tambah') || isset($barang_edit)) ? '8' : '12' }} grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang</h4>
                <form id="form-label" action="{{ route('label.form') }}" method="POST" style="display:none;">
                @csrf
                <div id="label-checkboxes"></div>
                </form>

                <div class="mb-3 d-flex justify-content-between">
                    @if(!request()->has('tambah') && !isset($barang_edit))
                    <a href="{{ route('barang.index') }}?tambah=true" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Barang
                    </a>
                    @else
                    <span></span>
                    @endif
                    <button type="button" onclick="cetakLabel()" class="btn btn-gradient-success btn-sm">
                        <i class="mdi mdi-printer"></i> Cetak Label
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="tableBarang" class="table table-hover table-barang" style="width:100%">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll"></th>
                                <th>#</th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangs as $index => $item)
                            <tr>
                                <td><input type="checkbox" class="barang-check" value="{{ $item->id_barang }}"></td>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->id_barang }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    <span class="badge bg-gradient-success">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('barang.edit', $item->id_barang) }}"
                                       class="btn btn-gradient-warning btn-sm btn-icon-text">
                                        <i class="mdi mdi-pencil btn-icon-prepend"></i> Edit
                                    </a>
                                    <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST"
                                          style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon-text">
                                            <i class="mdi mdi-delete btn-icon-prepend"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            {{-- Biarkan tbody kosong agar DataTables menampilkan emptyTable tanpa warning kolom --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- hidden form delete sudah tidak diperlukan --}}

@endsection

@push('script')
<script>
$(document).ready(function () {
    // Inisialisasi DataTables
    $('#tableBarang').DataTable({
        columnDefs: [
            { orderable: false, targets: [0, 5] } // kolom checkbox & aksi tidak bisa di-sort
        ],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            paginate: { previous: 'Sebelumnya', next: 'Selanjutnya' },
            zeroRecords: 'Tidak ada data yang cocok',
            emptyTable: 'Tidak ada data barang'
        }
    });

    // Check all checkbox
    $('#checkAll').on('click', function () {
        $('.barang-check').prop('checked', this.checked);
    });
});

function cetakLabel() {
    const checked = document.querySelectorAll('.barang-check:checked');
    if (checked.length === 0) {
        alert('Pilih minimal satu barang untuk dicetak label-nya.');
        return;
    }
    const container = document.getElementById('label-checkboxes');
    container.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'barang_ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
    document.getElementById('form-label').submit();
};

    window.handleSubmit = function() {
        let form = document.getElementById('form-barang');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Memproses...
        `;

        form.submit();
    };


</script>
@endpush