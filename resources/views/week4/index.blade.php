
{{-- Baris 1: Label untuk input --}}
<label for="name">Name:</label>

{{-- Baris 2: Input text, id="myIdname" dipakai AJAX untuk ambil nilainya --}}
<input type="text" id="myIdname">

{{-- Baris 3-5: Tombol dibungkus span agar bisa diganti isinya via jQuery --}}
<span id="subbutton">
    <button type="button"           {{-- type="button" → tidak reload halaman! --}}
            id="myButton"
            onclick="submitText()"> {{-- panggil fungsi submitText() saat diklik --}}
        Submit
    </button>
</span>

{{-- Baris 6: Spasi --}}
<br><br>

{{-- Baris 8: Tempat kosong untuk menampilkan response dari server --}}
<span id="freetxt"></span>

{{-- Baris 10: Library jQuery — wajib ada sebelum kode $.ajax() --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- Baris 11: Library SweetAlert2 untuk notifikasi popup --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Baris 13: Definisi fungsi yang dipanggil saat tombol diklik
    function submitText() {

        // Baris 14: Ambil elemen span yang membungkus tombol
        var btn = $('#subbutton');

        // Baris 15: Ubah isi span jadi teks "Submitting..."
        // supaya user tahu request sedang diproses
        btn.html('Submitting...');

        // Baris 16: Ambil nilai yang diketik user di input
        // .val() → jQuery method untuk ambil value input
        var name = $('#myIdname').val();

        // Baris 17: Mulai konstruksi AJAX
        $.ajax({

            // Baris 18: URL tujuan request
            // route('week4.ajax_submit') → generate URL dari nama route
            // lebih aman daripada nulis '/week4/ajax_submit' manual
            url: "{{ route('week4.ajax_submit') }}",

            // Baris 19: Method POST karena kita mengirim data ke server
            type: "POST",

            // Baris 20: Data yang dikirim ke server
            data: {
                // Baris 21: WAJIB ada untuk method POST di Laravel!
                // Tanpa ini → Laravel tolak request dengan error 419
                _token: "{{ csrf_token() }}",

                // Baris 22: Kirim nilai name yang tadi diambil dari input
                name: name
            },

            // Baris 24: Fungsi ini jalan kalau server respons HTTP 200
            success: function(response) {

                // Baris 25: Kembalikan tombol Submit seperti semula
                btn.html('<button type="button" id="myButton" onclick="submitText()">Submit</button>');

                // Baris 26: Tampilkan response di console browser
                // Buka DevTools → tab Console untuk lihat hasilnya
                // Gunakan ini untuk debugging!
                console.log(response);

                // Baris 27-32: Cek apakah status response adalah 'success'
                if (response.status === 'success') {

                    // Isi span #freetxt dengan nama yang dikembalikan server
                    // response.data.name → akses nested JSON object
                    $('#freetxt').html(response.data.name);

                    // Tampilkan popup sukses pakai SweetAlert2
                    Swal.fire(
                        'Success!',                        // judul
                        'Your data has been submitted.',   // pesan
                        'success'                          // icon: success/error/warning
                    );

                    // Kosongkan input setelah berhasil submit
                    $('#myIdname').val('');

                } else {
                    // Kalau status bukan 'success' meski HTTP 200
                    Swal.fire('Error!', 'There was an error submitting your data.', 'error');
                }
            },

            // Baris 34: Fungsi ini jalan kalau request gagal (404, 419, 500, dll)
            error: function(xhr) {

                // Baris 35: Kembalikan tombol Submit seperti semula
                btn.html('<button type="button" id="myButton" onclick="submitText()">Submit</button>');

                // Baris 36-40: Tampilkan popup error
                Swal.fire(
                    'Error!',
                    'There was an error submitting your data.',
                    'error'
                );
            }

        }); // tutup $.ajax()

    } // tutup function submitText()
</script>