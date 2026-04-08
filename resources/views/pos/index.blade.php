

@extends('layouts.main') 

@section('content')
<div class="container mt-4">
<div class="card">
<div class="card-header"><h5>🛒 Point of Sales (POS)</h5></div>
<div class="card-body">

    {{-- TAB Ajax vs Axios --}}
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

        {{-- ========== TAB AJAX JQUERY ========== --}}
        <div class="tab-pane fade show active" id="tab-ajax">

            <div class="row mb-3">
                <div class="col-md-6">

                    <div class="mb-2">
                        <label>Kode Barang</label>
                        <input type="text"
                               id="kode-ajax"
                               class="form-control"
                               placeholder="Ketik kode lalu tekan Enter"
                               onkeydown="if(event.key==='Enter') cariBarangAjax()">
                    </div>

                    <div class="mb-2">
                        <label>Nama Barang</label>
                        <input type="text"
                               id="nama-ajax"
                               class="form-control"
                               readonly
                               style="background:#fff5f5">
                    </div>

                    <div class="mb-2">
                        <label>Harga Barang</label>
                        <input type="number"
                               id="harga-ajax"
                               class="form-control"
                               readonly
                               style="background:#fff5f5">
                    </div>

                    <div class="mb-2">
                        <label>Jumlah</label>
                        <input type="number"
                               id="jumlah-ajax"
                               class="form-control"
                               min="1"
                               value="1"
                               oninput="cekTombolAjax()">
                    </div>

                    <span id="btn-tambah-wrap-ajax">
                        <button id="btn-tambah-ajax"
                                class="btn btn-success"
                                disabled
                                onclick="tambahKeTableAjax()">
                            Tambahkan
                        </button>
                    </span>

                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-ajax"></tbody>
            </table>

            <div class="text-end mb-3">
                <strong>Total: Rp <span id="total-ajax">0</span></strong>
            </div>

            <div class="text-end">
                <span id="btn-bayar-wrap-ajax">
                    <button id="btn-bayar-ajax"
                            class="btn btn-primary"
                            disabled
                            onclick="bayarAjax()">
                        Bayar
                    </button>
                </span>
            </div>

        </div>{{-- end tab-ajax --}}


        {{-- ========== TAB AXIOS ========== --}}
        <div class="tab-pane fade" id="tab-axios">

            <div class="row mb-3">
                <div class="col-md-6">

                    <div class="mb-2">
                        <label>Kode Barang</label>
                        <input type="text"
                               id="kode-axios"
                               class="form-control"
                               placeholder="Ketik kode lalu tekan Enter"
                               onkeydown="if(event.key==='Enter') cariBarangAxios()">
                    </div>

                    <div class="mb-2">
                        <label>Nama Barang</label>
                        <input type="text"
                               id="nama-axios"
                               class="form-control"
                               readonly
                               style="background:#fff5f5">
                    </div>

                    <div class="mb-2">
                        <label>Harga Barang</label>
                        <input type="number"
                               id="harga-axios"
                               class="form-control"
                               readonly
                               style="background:#fff5f5">
                    </div>

                    <div class="mb-2">
                        <label>Jumlah</label>
                        <input type="number"
                               id="jumlah-axios"
                               class="form-control"
                               min="1"
                               value="1"
                               oninput="cekTombolAxios()">
                    </div>

                    <span id="btn-tambah-wrap-axios">
                        <button id="btn-tambah-axios"
                                class="btn btn-success"
                                disabled
                                onclick="tambahKeTableAxios()">
                            Tambahkan
                        </button>
                    </span>

                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-axios"></tbody>
            </table>

            <div class="text-end mb-3">
                <strong>Total: Rp <span id="total-axios">0</span></strong>
            </div>

            <div class="text-end">
                <span id="btn-bayar-wrap-axios">
                    <button id="btn-bayar-axios"
                            class="btn btn-primary"
                            disabled
                            onclick="bayarAxios()">
                        Bayar
                    </button>
                </span>
            </div>

        </div>{{-- end tab-axios --}}

    </div>{{-- end tab-content --}}
</div>
</div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.APP = {
        routeCariBarang : '{{ url("pos/cari-barang") }}',
        routeBayar      : '{{ route("pos.bayar") }}',
        csrfToken       : '{{ csrf_token() }}'
    };
</script>

{{-- Load file JS  --}}
<script src="{{ asset('js/pos.js') }}"></script>

@endsection