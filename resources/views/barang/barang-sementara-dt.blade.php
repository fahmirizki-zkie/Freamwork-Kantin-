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
            <i class="mdi mdi-cart"></i>
        </span> Barang Sementara (DataTables)
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

                <form id="form-barang-sementara-dt">

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

    {{-- KOLOM KANAN: TABEL DATATABLES --}}
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
                    <table id="tabelBarangDT" class="table table-hover table-barang" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- MODAL EDIT & HAPUS --}}
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
                        <input type="text" id="modalId" class="form-control" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" id="modalNama" class="form-control" placeholder="Nama barang" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Harga <span class="text-danger">*</span></label>
                        <input type="number" id="modalHarga" class="form-control" placeholder="0" min="0" required>
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

    let nomorUrut = 1;

    // Menyimpan ROW DataTables yang sedang diklik
    // Berbeda dengan halaman 1 yang menyimpan index,
    // di sini kita simpan objek row DataTables langsung
    let selectedDtRow = null;

    // ============================================================
    // Inisialisasi DataTables
    // ============================================================
    $(document).ready(function () {

        window.dataTable = $('#tabelBarangDT').DataTable({
            data: [],
            columns: [
                { title: '#' },
                { title: 'ID Barang' },
                { title: 'Nama Barang' },
                { title: 'Harga' }
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                paginate: { previous: 'Sebelumnya', next: 'Selanjutnya' },
                zeroRecords: 'Belum ada data. Silahkan tambah barang!',
                emptyTable: 'Belum ada data. Silahkan tambah barang!'
            }
        });

        // ✅ PERBEDAAN DENGAN HALAMAN 1:
        // Event klik row di DataTables HARUS didaftarkan
        // menggunakan cara ini (delegated event) karena
        // row ditambahkan secara dinamis setelah tabel diinisialisasi.
        // Kalau pakai onclick langsung di <tr> seperti halaman 1,
        // DataTables tidak akan mengenali row baru yang ditambahkan!
        $('#tabelBarangDT tbody').on('click', 'tr', function () {
            bukaModal(this);
        });

    });

    // ============================================================
    // FUNGSI TAMBAH
    // ============================================================
    function handleTambah() {

        let form = document.getElementById('form-barang-sementara-dt');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        tampilkanSpinner('btnTambah');

        let nama           = document.getElementById('inputNama').value;
        let harga          = document.getElementById('inputHarga').value;
        let idBarang       = 'BRG' + String(nomorUrut).padStart(3, '0');
        let hargaFormatted = 'Rp ' + parseInt(harga).toLocaleString('id-ID');

        setTimeout(function () {

            // Tambah row ke DataTables menggunakan .row.add().draw()
            window.dataTable.row.add([
                nomorUrut,
                idBarang,
                nama,
                hargaFormatted
            ]).draw();

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

        // ✅ PERBEDAAN DENGAN HALAMAN 1:
        // Di DataTables, kita ambil data row menggunakan
        // window.dataTable.row(el).data()
        // bukan dari atribut data-* seperti halaman 1
        // karena DataTables menyimpan datanya sendiri secara internal
        let rowData = window.dataTable.row(el).data();

        // rowData adalah array sesuai urutan kolom:
        // rowData[0] = # (nomor urut)
        // rowData[1] = ID Barang
        // rowData[2] = Nama Barang
        // rowData[3] = Harga (sudah diformat "Rp 10.000")

        // Simpan objek row DataTables untuk dipakai handleUbah & handleHapus
        selectedDtRow = window.dataTable.row(el);

        // Isi form modal
        document.getElementById('modalId').value = rowData[1];
        document.getElementById('modalNama').value = rowData[2];

        // Harga perlu dibersihkan dari format "Rp 10.000" → "10000"
        // supaya bisa dimasukkan ke input type="number"
        let hargaBersih = rowData[3].replace('Rp ', '').replace(/\./g, '');
        document.getElementById('modalHarga').value = hargaBersih;

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
        let hargaFormatted = 'Rp ' + parseInt(hargaBaru).toLocaleString('id-ID');

        setTimeout(function () {

            // ✅ PERBEDAAN DENGAN HALAMAN 1:
            // Di DataTables, update data row menggunakan
            // selectedDtRow.data([...]).draw()
            // bukan row.cells[2].innerHTML seperti halaman 1

            // Ambil data lama dulu supaya nomor urut dan ID tidak berubah
            let dataLama = selectedDtRow.data();

            // Update data — nomor urut (index 0) dan ID (index 1) tetap sama
            selectedDtRow.data([
                dataLama[0],    // # tetap
                dataLama[1],    // ID Barang tetap
                namaBaru,       // Nama diupdate
                hargaFormatted  // Harga diupdate
            ]).draw();

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

            // ✅ PERBEDAAN DENGAN HALAMAN 1:
            // Di DataTables, hapus row menggunakan
            // selectedDtRow.remove().draw()
            // bukan row.remove() seperti halaman 1
            selectedDtRow.remove().draw();

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