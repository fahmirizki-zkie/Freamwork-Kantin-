@extends('layouts.main')

@push('style')
<style>
    /* Cursor pointer saat hover row — ketentuan tugas 3 */
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
            <i class="mdi mdi-cart"></i>
        </span> Barang Sementara
    </h3>
</div>

<div class="row">

    {{-- KOLOM KIRI: FORM INPUT --}}
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-primary">
                    <i class="mdi mdi-plus-circle"></i> Tambah Barang
                </h4>

                <form id="form-barang-sementara">

                    <div class="form-group">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="inputNama"
                            class="form-control"
                            placeholder="Nama barang"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Harga <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            id="inputHarga"
                            class="form-control"
                            placeholder="0"
                            min="0"
                            required>
                    </div>

                    <div class="form-group mt-3">
                        <button
                            type="button"
                            id="btnTambah"
                            onclick="handleTambah()"
                            class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Tambah
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: TABEL DATA --}}
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang Sementara</h4>
                <p class="text-muted small">
                    <i class="mdi mdi-information-outline"></i>
                    Data di halaman ini <strong>tidak tersimpan ke database</strong>.
                    Klik baris untuk edit atau hapus.
                </p>

                <div class="table-responsive">
                    <table class="table table-hover table-barang" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBody">
                            <tr id="rowKosong">
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada data. Silahkan tambah barang!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ============================================================ --}}
{{-- MODAL EDIT & HAPUS                                           --}}
{{-- Modal ini tersembunyi, muncul saat user klik sebuah row     --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalEditHapus" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">
                    <i class="mdi mdi-pencil"></i> Edit / Hapus Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="form-modal">

                    <div class="form-group mb-3">
                        <label>ID Barang</label>
                        {{-- ID Barang readonly — tidak bisa diubah --}}
                        <input
                            type="text"
                            id="modalId"
                            class="form-control"
                            readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="modalNama"
                            class="form-control"
                            placeholder="Nama barang"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Harga <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            id="modalHarga"
                            class="form-control"
                            placeholder="0"
                            min="0"
                            required>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnHapus" onclick="handleHapus()" class="btn btn-danger btn-sm">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
                <button type="button" id="btnUbah" onclick="handleUbah()" class="btn btn-warning btn-sm">
                    <i class="mdi mdi-content-save"></i> Ubah
                </button>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i> Batal
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('script')
<script>

    // Nomor urut untuk ID barang otomatis
    let nomorUrut = 1;

    // Menyimpan index row yang sedang diklik
    // Dipakai saat proses Ubah dan Hapus
    let selectedRowIndex = null;

    // ============================================================
    // FUNGSI TAMBAH
    // ============================================================
    function handleTambah() {

        let form = document.getElementById('form-barang-sementara');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        tampilkanSpinner('btnTambah');

        let nama     = document.getElementById('inputNama').value;
        let harga    = document.getElementById('inputHarga').value;
        let idBarang = 'BRG' + String(nomorUrut).padStart(3, '0');
        let hargaFormatted = parseInt(harga).toLocaleString('id-ID');

        setTimeout(function () {

            let rowKosong = document.getElementById('rowKosong');
            if (rowKosong) rowKosong.remove();

            let tbody = document.getElementById('tabelBody');

            // data-index, data-id, data-nama, data-harga
            // dipakai untuk menyimpan data di elemen HTML
            // supaya bisa diambil saat row diklik
            tbody.innerHTML += `
                <tr
                    data-index="${nomorUrut}"
                    data-id="${idBarang}"
                    data-nama="${nama}"
                    data-harga="${harga}"
                    onclick="bukaModal(this)">
                    <td>${nomorUrut}</td>
                    <td>${idBarang}</td>
                    <td>${nama}</td>
                    <td><span class="badge bg-gradient-success">Rp ${hargaFormatted}</span></td>
                </tr>
            `;

            nomorUrut++;
            document.getElementById('inputNama').value  = '';
            document.getElementById('inputHarga').value = '';
            kembalikanTombol('btnTambah', '<i class="mdi mdi-plus"></i> Tambah');

        }, 1000);
    }

    // ============================================================
    // FUNGSI BUKA MODAL — Dipanggil saat user klik row
    // el = elemen <tr> yang diklik
    // ============================================================
    function bukaModal(el) {

        // Ambil data dari atribut data-* pada row yang diklik
        selectedRowIndex = el.getAttribute('data-index');

        document.getElementById('modalId').value    = el.getAttribute('data-id');
        document.getElementById('modalNama').value  = el.getAttribute('data-nama');
        document.getElementById('modalHarga').value = el.getAttribute('data-harga');

        // Tampilkan modal Bootstrap
        new bootstrap.Modal(document.getElementById('modalEditHapus')).show();
    }

    // ============================================================
    // FUNGSI UBAH
    // ============================================================
    function handleUbah() {

        let form = document.getElementById('form-modal');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        tampilkanSpinner('btnUbah');

        let namaBaru       = document.getElementById('modalNama').value;
        let hargaBaru      = document.getElementById('modalHarga').value;
        let hargaFormatted = parseInt(hargaBaru).toLocaleString('id-ID');

        setTimeout(function () {

            // Cari row berdasarkan data-index
            let row = document.querySelector(`tr[data-index="${selectedRowIndex}"]`);

            if (row) {
                // Update data-* supaya kalau diklik lagi datanya sudah terbaru
                row.setAttribute('data-nama', namaBaru);
                row.setAttribute('data-harga', hargaBaru);

                // Update tampilan cell — index 2 = nama, index 3 = harga
                row.cells[2].innerHTML = namaBaru;
                row.cells[3].innerHTML = `<span class="badge bg-gradient-success">Rp ${hargaFormatted}</span>`;
            }

            tutupModal();
            kembalikanTombol('btnUbah', '<i class="mdi mdi-content-save"></i> Ubah');

        }, 1000);
    }

    // ============================================================
    // FUNGSI HAPUS
    // ============================================================
    function handleHapus() {

        tampilkanSpinner('btnHapus');

        setTimeout(function () {

            // Cari row berdasarkan data-index lalu hapus
            let row = document.querySelector(`tr[data-index="${selectedRowIndex}"]`);
            if (row) row.remove();

            // Kalau tabel kosong, tampilkan kembali baris "Belum ada data"
            let tbody = document.getElementById('tabelBody');
            if (tbody.querySelectorAll('tr').length === 0) {
                tbody.innerHTML = `
                    <tr id="rowKosong">
                        <td colspan="4" class="text-center text-muted">
                            Belum ada data. Silahkan tambah barang!
                        </td>
                    </tr>
                `;
            }

            tutupModal();
            kembalikanTombol('btnHapus', '<i class="mdi mdi-delete"></i> Hapus');

        }, 1000);
    }

    // ============================================================
    // FUNGSI HELPER
    // ============================================================
    function tutupModal() {
        let modalEl = document.getElementById('modalEditHapus');
        bootstrap.Modal.getInstance(modalEl).hide();
    }

    function tampilkanSpinner(idBtn) {
        let btn = document.getElementById(idBtn);
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Memproses...`;
    }

    function kembalikanTombol(idBtn, labelAsli) {
        let btn = document.getElementById(idBtn);
        btn.disabled = false;
        btn.innerHTML = labelAsli;
    }

</script>
@endpush