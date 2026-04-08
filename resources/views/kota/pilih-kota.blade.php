@extends('layouts.main')

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da;
    border-radius: 4px;
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    color: #495057;
    padding-left: 10px;
    padding-right: 30px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    top: 1px !important;
}
.select2-container {
    width: 100% !important;
}
#kotaTerpilihBiasa,
#kotaTerpilihSelect2 {
    word-break: break-word;
    white-space: normal;
    overflow-wrap: break-word;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>
@endpush

@section('content')

<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-map-marker"></i>
        </span> Pilih Kota
    </h3>
</div>

<div class="row">

    {{-- CARD 1 — SELECT BIASA --}}
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Select</h4>
            </div>
            <div class="card-body">

                <form id="form-kota-biasa">
                    <div class="form-group">
                        <label>Tambah Kota</label>
                        <div class="input-group">
                            <input
                                type="text"
                                id="inputKotaBiasa"
                                class="form-control"
                                placeholder="Nama kota..."
                                required>
                            <button
                                type="button"
                                id="btnTambahKotaBiasa"
                                onclick="tambahKota('biasa')"
                                class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>

                <div class="form-group mt-3">
                    <label>Pilih Kota</label>
                    <select id="selectKotaBiasa" class="form-control">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div class="mt-3">
                    <label>Kota Terpilih</label>
                    <div id="kotaTerpilihBiasa" class="alert alert-info mt-1">
                        Belum ada kota yang dipilih.
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- CARD 2 — SELECT2 --}}
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Select 2</h4>
            </div>
            <div class="card-body">

                <form id="form-kota-select2">
                    <div class="form-group">
                        <label>Tambah Kota</label>
                        <div class="input-group">
                            <input
                                type="text"
                                id="inputKotaSelect2"
                                class="form-control"
                                placeholder="Nama kota..."
                                required>
                            <button
                                type="button"
                                id="btnTambahKotaSelect2"
                                onclick="tambahKota('select2')"
                                class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>

                <div class="form-group mt-3">
                    <label>Pilih Kota</label>
                    <select id="selectKotaSelect2" class="form-control">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div class="mt-3">
                    <label>Kota Terpilih</label>
                    <div id="kotaTerpilihSelect2" class="alert alert-info mt-1">
                        Belum ada kota yang dipilih.
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@push('script')
{{-- Select2 JS CDN — harus setelah jQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>

    $(document).ready(function () {

        // Inisialisasi Select2
        $('#selectKotaSelect2').select2({
            placeholder: '-- Pilih Kota --',
            allowClear: true
        });

        // Event change Select biasa
        $('#selectKotaBiasa').on('change', function () {
            kotaTerpilih('biasa');
        });

        // Event change Select2
        $('#selectKotaSelect2').on('change', function () {
            kotaTerpilih('select2');
        });

    });

    function tambahKota(tipe) {

        let idInput  = tipe === 'biasa' ? 'inputKotaBiasa'     : 'inputKotaSelect2';
        let idSelect = tipe === 'biasa' ? 'selectKotaBiasa'    : 'selectKotaSelect2';
        let idForm   = tipe === 'biasa' ? 'form-kota-biasa'    : 'form-kota-select2';
        let idBtn    = tipe === 'biasa' ? 'btnTambahKotaBiasa' : 'btnTambahKotaSelect2';

        let form  = document.getElementById(idForm);
        let input = document.getElementById(idInput);

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        tampilkanSpinner(idBtn);

        let namaKota = input.value.trim();

        setTimeout(function () {

            let value = namaKota.toLowerCase().replace(/\s+/g, '-');

            if (tipe === 'biasa') {
                let select = document.getElementById(idSelect);
                let option = document.createElement('option');
                option.value       = value;
                option.textContent = namaKota;
                select.appendChild(option);
            } else {
                let option = new Option(namaKota, value, false, false);
                $('#' + idSelect).append(option).trigger('change.select2');
            }

            input.value = '';
            kembalikanTombol(idBtn, '<i class="mdi mdi-plus"></i> Tambah');

        }, 800);
    }

    function kotaTerpilih(tipe) {

        let idSelect = tipe === 'biasa' ? 'selectKotaBiasa'   : 'selectKotaSelect2';
        let idTampil = tipe === 'biasa' ? 'kotaTerpilihBiasa' : 'kotaTerpilihSelect2';

        let nilai = tipe === 'biasa'
            ? document.getElementById(idSelect).value
            : $('#' + idSelect).val();

        let teksKota = tipe === 'biasa'
            ? document.getElementById(idSelect).options[document.getElementById(idSelect).selectedIndex].text
            : $('#' + idSelect + ' option:selected').text();

        let tampilDiv = document.getElementById(idTampil);

        if (!nilai || nilai === '') {
            tampilDiv.className = 'alert alert-info mt-1';
            tampilDiv.innerHTML = 'Belum ada kota yang dipilih.';
        } else {
            tampilDiv.className = 'alert alert-success mt-1';
            tampilDiv.innerHTML = `<i class="mdi mdi-map-marker"></i> <strong>${teksKota}</strong>`;
        }
    }

    function tampilkanSpinner(idBtn) {
        let btn = document.getElementById(idBtn);
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span>`;
    }

    function kembalikanTombol(idBtn, labelAsli) {
        let btn = document.getElementById(idBtn);
        btn.disabled = false;
        btn.innerHTML = labelAsli;
    }

</script>
@endpush