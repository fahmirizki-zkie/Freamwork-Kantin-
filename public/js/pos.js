// ============================================================
// HELPER: Format angka ke Rupiah
// ============================================================
function formatRupiah(angka) {
    return parseInt(angka).toLocaleString('id-ID');
}


// ============================================================
// HELPER: Hitung ulang total dari semua subtotal di tabel
// Dipanggil setiap kali ada perubahan di tabel (req. h)
// ============================================================
function hitungTotal(suffix) {
    let total = 0;
    document.querySelectorAll(`.subtotal-${suffix}`).forEach(function(el) {
        total += parseInt(el.dataset.value) || 0;
    });
    document.getElementById(`total-${suffix}`).innerText = formatRupiah(total);

    // Aktifkan tombol Bayar kalau ada item di tabel
    const tbody   = document.getElementById(`tbody-${suffix}`);
    const btnBayar = document.getElementById(`btn-bayar-${suffix}`);
    btnBayar.disabled = tbody.rows.length === 0;
}


// ============================================================
// HELPER: Cek tombol Tambahkan boleh aktif atau tidak
// Aktif hanya jika barang ditemukan DAN jumlah > 0 (req. d)
// ============================================================
function cekTombolAjax() {
    const nama   = document.getElementById('nama-ajax').value;
    const jumlah = parseInt(document.getElementById('jumlah-ajax').value);
    document.getElementById('btn-tambah-ajax').disabled = !(nama && jumlah > 0);
}

function cekTombolAxios() {
    const nama   = document.getElementById('nama-axios').value;
    const jumlah = parseInt(document.getElementById('jumlah-axios').value);
    document.getElementById('btn-tambah-axios').disabled = !(nama && jumlah > 0);
}


// ============================================================
// HELPER: Reset form input setelah tambah atau bayar
// ============================================================
function resetForm(suffix) {
    document.getElementById(`kode-${suffix}`).value   = '';
    document.getElementById(`nama-${suffix}`).value   = '';
    document.getElementById(`harga-${suffix}`).value  = '';
    document.getElementById(`jumlah-${suffix}`).value = 1;
    document.getElementById(`btn-tambah-${suffix}`).disabled = true;
}


// ============================================================
// HELPER: Update subtotal saat jumlah di tabel diubah (req. g & h)
// ============================================================
function updateSubtotal(inputEl, suffix) {
    const jumlah   = parseInt(inputEl.value) || 0;
    const harga    = parseInt(inputEl.dataset.harga);
    const subtotal = harga * jumlah;
    const row      = document.getElementById(`row-${suffix}-${inputEl.dataset.kode}`);
    const spanSub  = row.querySelector(`.subtotal-${suffix}`);
    spanSub.innerText     = 'Rp ' + formatRupiah(subtotal);
    spanSub.dataset.value = subtotal;
    hitungTotal(suffix);
}


// ============================================================
// HELPER: Hapus baris dari tabel (req. g)
// ============================================================
function hapusBaris(rowId, suffix) {
    document.getElementById(rowId).remove();
    hitungTotal(suffix);
}


// ============================================================
// HELPER: Tambah atau update baris di tabel
// req (f): kalau kode sama → update jumlah & subtotal saja
// req (g): ada tombol hapus & input jumlah bisa diubah
// ============================================================
function tambahKeTabel(suffix, kode, nama, harga, jumlah) {
    const tbody    = document.getElementById(`tbody-${suffix}`);
    const subtotal = harga * jumlah;

    // Cek apakah kode barang sudah ada di tabel (req. f)
    const barisExisting = document.getElementById(`row-${suffix}-${kode}`);

    if (barisExisting) {
        // Kode sama → update jumlah dan subtotal saja
        const inputJumlah = barisExisting.querySelector('.input-jumlah');
        const newJumlah   = parseInt(inputJumlah.value) + jumlah;
        const newSubtotal = harga * newJumlah;

        inputJumlah.value = newJumlah;
        const subtotalEl  = barisExisting.querySelector(`.subtotal-${suffix}`);
        subtotalEl.innerText     = 'Rp ' + formatRupiah(newSubtotal);
        subtotalEl.dataset.value = newSubtotal;

    } else {
        // Kode baru → tambah baris baru
        const tr = document.createElement('tr');
        tr.id    = `row-${suffix}-${kode}`;
        tr.innerHTML = `
            <td>${kode}</td>
            <td>${nama}</td>
            <td>Rp ${formatRupiah(harga)}</td>
            <td>
                <input type="number"
                       class="form-control form-control-sm input-jumlah"
                       value="${jumlah}"
                       min="1"
                       data-harga="${harga}"
                       data-kode="${kode}"
                       style="width:80px"
                       oninput="updateSubtotal(this, '${suffix}')">
            </td>
            <td>
                <span class="subtotal-${suffix}" data-value="${subtotal}">
                    Rp ${formatRupiah(subtotal)}
                </span>
            </td>
            <td>
                <button class="btn btn-danger btn-sm"
                        onclick="hapusBaris('row-${suffix}-${kode}', '${suffix}')">
                    Hapus
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    // Hitung ulang total (req. h)
    hitungTotal(suffix);
}


// ============================================================
// VERSI AJAX JQUERY
// ============================================================

// Cari barang saat Enter ditekan (req. b)
function cariBarangAjax() {
    const kode = $('#kode-ajax').val().trim();
    if (!kode) return;

    $('#btn-tambah-ajax').prop('disabled', true);

    $.ajax({
        method: 'GET',
        // Pakai window.APP.routeCariBarang — bukan {{ }} lagi!
        url: window.APP.routeCariBarang + '/' + kode,
        success: function(response) {
            if (response.status === 'success') {
                $('#nama-ajax').val(response.data.nama);
                $('#harga-ajax').val(response.data.harga);
                $('#jumlah-ajax').val(1); // default jumlah = 1 (req. c)
                cekTombolAjax();          // cek apakah Tambahkan bisa aktif (req. d)
            } else {
                $('#nama-ajax').val('');
                $('#harga-ajax').val('');
                $('#jumlah-ajax').val(1);
                $('#btn-tambah-ajax').prop('disabled', true);
                Swal.fire('Tidak Ditemukan', response.message, 'warning');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Terjadi kesalahan', 'error');
        }
    });
}

// Tambahkan barang ke tabel saat klik Tambahkan (req. e)
function tambahKeTableAjax() {
    const kode   = $('#kode-ajax').val();
    const nama   = $('#nama-ajax').val();
    const harga  = parseInt($('#harga-ajax').val());
    const jumlah = parseInt($('#jumlah-ajax').val());

    tambahKeTabel('ajax', kode, nama, harga, jumlah);
    resetForm('ajax');
}

// Simpan transaksi ke DB saat klik Bayar (req. i)
function bayarAjax() {
    const items = [];
    document.querySelectorAll('#tbody-ajax tr').forEach(function(row) {
        items.push({
            id_barang : row.cells[0].innerText,
            nama      : row.cells[1].innerText,
            harga     : parseInt(row.querySelector('.input-jumlah').dataset.harga),
            jumlah    : parseInt(row.querySelector('.input-jumlah').value),
            subtotal  : parseInt(row.querySelector('.subtotal-ajax').dataset.value)
        });
    });

    const total = items.reduce((sum, i) => sum + i.subtotal, 0);

    // Disable tombol Bayar saat proses (req. l)
    $('#btn-bayar-wrap-ajax').html(
        '<button class="btn btn-secondary" disabled>Memproses...</button>'
    );

    $.ajax({
        method: 'POST',
        url: window.APP.routeBayar,   // pakai window.APP
        data: {
            _token : window.APP.csrfToken, // pakai window.APP
            items  : items,
            total  : total
        },
        success: function(response) {
            if (response.status === 'success') {
                // req. j: notif sukses → kosongkan halaman
                Swal.fire('Berhasil!', 'Transaksi berhasil disimpan!', 'success')
                .then(function() {
                    document.getElementById('tbody-ajax').innerHTML = '';
                    resetForm('ajax');
                    hitungTotal('ajax');
                });
            }
            // Kembalikan tombol Bayar
            $('#btn-bayar-wrap-ajax').html(
                '<button id="btn-bayar-ajax" class="btn btn-primary" disabled onclick="bayarAjax()">Bayar</button>'
            );
        },
        error: function(xhr) {
            Swal.fire('Error', 'Gagal menyimpan transaksi', 'error');
            $('#btn-bayar-wrap-ajax').html(
                '<button id="btn-bayar-ajax" class="btn btn-primary" disabled onclick="bayarAjax()">Bayar</button>'
            );
        }
    });
}


// ============================================================
// VERSI AXIOS
// ============================================================

// Cari barang saat Enter ditekan (req. b)
function cariBarangAxios() {
    const kode = document.getElementById('kode-axios').value.trim();
    if (!kode) return;

    document.getElementById('btn-tambah-axios').disabled = true;

    // Pakai window.APP.routeCariBarang
    axios.get(window.APP.routeCariBarang + '/' + kode)
    .then(function(response) {
        const result = response.data; // ⚠️ Axios: JSON ada di response.data
        if (result.status === 'success') {
            document.getElementById('nama-axios').value   = result.data.nama;
            document.getElementById('harga-axios').value  = result.data.harga;
            document.getElementById('jumlah-axios').value = 1;
            cekTombolAxios();
        } else {
            document.getElementById('nama-axios').value  = '';
            document.getElementById('harga-axios').value = '';
            document.getElementById('btn-tambah-axios').disabled = true;
            Swal.fire('Tidak Ditemukan', result.message, 'warning');
        }
    })
    .catch(function(error) {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
}

// Tambahkan barang ke tabel saat klik Tambahkan (req. e)
function tambahKeTableAxios() {
    const kode   = document.getElementById('kode-axios').value;
    const nama   = document.getElementById('nama-axios').value;
    const harga  = parseInt(document.getElementById('harga-axios').value);
    const jumlah = parseInt(document.getElementById('jumlah-axios').value);

    tambahKeTabel('axios', kode, nama, harga, jumlah);
    resetForm('axios');
}

// Simpan transaksi ke DB saat klik Bayar (req. i)
function bayarAxios() {
    const items = [];
    document.querySelectorAll('#tbody-axios tr').forEach(function(row) {
        items.push({
            id_barang : row.cells[0].innerText,
            nama      : row.cells[1].innerText,
            harga     : parseInt(row.querySelector('.input-jumlah').dataset.harga),
            jumlah    : parseInt(row.querySelector('.input-jumlah').value),
            subtotal  : parseInt(row.querySelector('.subtotal-axios').dataset.value)
        });
    });

    const total = items.reduce((sum, i) => sum + i.subtotal, 0);

    const btnWrap = document.getElementById('btn-bayar-wrap-axios');
    btnWrap.innerHTML = '<button class="btn btn-secondary" disabled>Memproses...</button>';

    // Pakai window.APP.routeBayar dan window.APP.csrfToken
    axios.post(window.APP.routeBayar, {
        items : items,
        total : total
    }, {
        headers: {
            'X-CSRF-TOKEN': window.APP.csrfToken
        }
    })
    .then(function(response) {
        const result = response.data;
        if (result.status === 'success') {
            Swal.fire('Berhasil!', 'Transaksi berhasil disimpan!', 'success')
            .then(function() {
                document.getElementById('tbody-axios').innerHTML = '';
                resetForm('axios');
                hitungTotal('axios');
            });
        }
        btnWrap.innerHTML = '<button id="btn-bayar-axios" class="btn btn-primary" disabled onclick="bayarAxios()">Bayar</button>';
    })
    .catch(function(error) {
        Swal.fire('Error', 'Gagal menyimpan transaksi', 'error');
        btnWrap.innerHTML = '<button id="btn-bayar-axios" class="btn btn-primary" disabled onclick="bayarAxios()">Bayar</button>';
    });
}