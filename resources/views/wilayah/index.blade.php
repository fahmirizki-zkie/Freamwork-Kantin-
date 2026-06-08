@extends('layouts.main') {{-- sesuaikan dengan layout project kamu --}}

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Dropdown Wilayah Indonesia</h5>
        </div>
        <div class="card-body">

            {{-- TAB untuk switching Ajax vs Axios --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-ajax">
                        jQuery AJAX
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-axios">
                        Axios
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ======== TAB AJAX JQUERY ======== --}}
                <div class="tab-pane fade show active" id="tab-ajax">

                    {{-- Provinsi: langsung dari controller, tidak pakai AJAX --}}
                    <div class="mb-3">
                        <label>Provinsi</label>
                        <select id="provinsi-ajax" class="form-select"
                                onchange="getKotaAjax(this.value)">
                            <option value="0">— Pilih Provinsi —</option>
                            @foreach ($provinsi as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kota: disabled dulu, aktif setelah provinsi dipilih --}}
                    <div class="mb-3">
                        <label>Kota / Kabupaten</label>
                        <select id="kota-ajax" class="form-select" disabled
                                onchange="getKecamatanAjax(this.value)">
                            <option value="0">— Pilih Kota —</option>
                        </select>
                    </div>

                    {{-- Kecamatan: disabled dulu --}}
                    <div class="mb-3">
                        <label>Kecamatan</label>
                        <select id="kecamatan-ajax" class="form-select" disabled
                                onchange="getKelurahanAjax(this.value)">
                            <option value="0">— Pilih Kecamatan —</option>
                        </select>
                    </div>

                    {{-- Kelurahan: disabled dulu --}}
                    <div class="mb-3">
                        <label>Kelurahan / Desa</label>
                        <select id="kelurahan-ajax" class="form-select" disabled>
                            <option value="0">— Pilih Kelurahan —</option>
                        </select>
                    </div>

                </div>

                {{-- ======== TAB AXIOS ======== --}}
                <div class="tab-pane fade" id="tab-axios">

                    <div class="mb-3">
                        <label>Provinsi</label>
                        <select id="provinsi-axios" class="form-select"
                                onchange="getKotaAxios(this.value)">
                            <option value="0">— Pilih Provinsi —</option>
                            @foreach ($provinsi as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Kota / Kabupaten</label>
                        <select id="kota-axios" class="form-select" disabled
                                onchange="getKecamatanAxios(this.value)">
                            <option value="0">— Pilih Kota —</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Kecamatan</label>
                        <select id="kecamatan-axios" class="form-select" disabled
                                onchange="getKelurahanAxios(this.value)">
                            <option value="0">— Pilih Kecamatan —</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Kelurahan / Desa</label>
                        <select id="kelurahan-axios" class="form-select" disabled>
                            <option value="0">— Pilih Kelurahan —</option>
                        </select>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

{{-- Library jQuery & Axios --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>

// ============================================================
// HELPER: Reset select jadi kosong + disabled
// Dipanggil setiap kali dropdown di atasnya berubah
// ============================================================
function resetSelect(id, label) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="0">— ${label} —</option>`;
    el.disabled = true;
}


// ============================================================
// VERSI AJAX JQUERY
// ============================================================

function getKotaAjax(provinsiId) {
    // Requirement (d): level 1 berubah → kosongkan level 2, 3, 4
    resetSelect('kota-ajax', 'Pilih Kota');
    resetSelect('kecamatan-ajax', 'Pilih Kecamatan');
    resetSelect('kelurahan-ajax', 'Pilih Kelurahan');

    if (provinsiId === '0') return; // stop kalau pilih default

    $.ajax({
        method: 'GET',
        url: '{{ route("wilayah.kota", ":id") }}'.replace(':id', provinsiId),
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="0">— Pilih Kota —</option>';
                // Loop data kota yang dikembalikan server
                response.data.forEach(function(item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                // Isi dropdown + aktifkan
                $('#kota-ajax').html(options).prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.log('Error:', xhr);
        }
    });
}

function getKecamatanAjax(kotaId) {
    // Requirement (e): level 2 berubah → kosongkan level 3, 4
    resetSelect('kecamatan-ajax', 'Pilih Kecamatan');
    resetSelect('kelurahan-ajax', 'Pilih Kelurahan');

    if (kotaId === '0') return;

    $.ajax({
        method: 'GET',
        url: '{{ route("wilayah.kecamatan", ":id") }}'.replace(':id', kotaId),
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="0">— Pilih Kecamatan —</option>';
                response.data.forEach(function(item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                $('#kecamatan-ajax').html(options).prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.log('Error:', xhr);
        }
    });
}

function getKelurahanAjax(kecamatanId) {
    resetSelect('kelurahan-ajax', 'Pilih Kelurahan');

    if (kecamatanId === '0') return;

    $.ajax({
        method: 'GET',
        url: '{{ route("wilayah.kelurahan", ":id") }}'.replace(':id', kecamatanId),
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="0">— Pilih Kelurahan —</option>';
                response.data.forEach(function(item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                $('#kelurahan-ajax').html(options).prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.log('Error:', xhr);
        }
    });
}


// ============================================================
// VERSI AXIOS
// ============================================================

function getKotaAxios(provinsiId) {
    resetSelect('kota-axios', 'Pilih Kota');
    resetSelect('kecamatan-axios', 'Pilih Kecamatan');
    resetSelect('kelurahan-axios', 'Pilih Kelurahan');

    if (provinsiId === '0') return;

    // axios.get() — lebih singkat dari $.ajax()
    axios.get('{{ url("wilayah/kota") }}/' + provinsiId)
    .then(function(response) {
        // ⚠️ Di Axios, JSON dari Laravel ada di response.data
        const result = response.data;
        if (result.status === 'success') {
            let options = '<option value="0">— Pilih Kota —</option>';
            result.data.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            const el = document.getElementById('kota-axios');
            el.innerHTML = options;
            el.disabled = false;
        }
    })
    .catch(function(error) {
        console.log('Error:', error);
    });
}

function getKecamatanAxios(kotaId) {
    resetSelect('kecamatan-axios', 'Pilih Kecamatan');
    resetSelect('kelurahan-axios', 'Pilih Kelurahan');

    if (kotaId === '0') return;

    axios.get('{{ url("wilayah/kecamatan") }}/' + kotaId)
    .then(function(response) {
        const result = response.data;
        if (result.status === 'success') {
            let options = '<option value="0">— Pilih Kecamatan —</option>';
            result.data.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            const el = document.getElementById('kecamatan-axios');
            el.innerHTML = options;
            el.disabled = false;
        }
    })
    .catch(function(error) {
        console.log('Error:', error);
    });
}

function getKelurahanAxios(kecamatanId) {
    resetSelect('kelurahan-axios', 'Pilih Kelurahan');

    if (kecamatanId === '0') return;

    axios.get('{{ url("wilayah/kelurahan") }}/' + kecamatanId)
    .then(function(response) {
        const result = response.data;
        if (result.status === 'success') {
            let options = '<option value="0">— Pilih Kelurahan —</option>';
            result.data.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            const el = document.getElementById('kelurahan-axios');
            el.innerHTML = options;
            el.disabled = false;
        }
    })
    .catch(function(error) {
        console.log('Error:', error);
    });
}

</script>

@endsection