<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Makanan - Kantin Online</title>

    {{-- Fonts & CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #F8FAFC;
            font-family: 'Inter', sans-serif;
            color: #0F172A;
        }
        .app-navbar {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .navbar-brand-custom {
            font-weight: 700;
            font-size: 1.25rem;
            color: #0F172A;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Cards */
        .card-custom {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .card-header-custom {
            background: #F8FAFC;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            font-weight: 600;
            font-size: 1.05rem;
            color: #1E293B;
        }
        .card-body-custom {
            padding: 1.5rem;
        }

        /* Forms */
        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .form-select-custom {
            border-radius: 0.75rem;
            border: 1px solid #CBD5E1;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            color: #0F172A;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }
        .form-select-custom:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            outline: none;
        }

        /* Menus */
        .menu-area-wrapper {
            background-color: #F1F5F9;
            min-height: 400px;
            border-radius: 0 0 1rem 1rem;
            padding: 1.5rem;
        }
        .menu-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
        }
        .menu-card:hover {
            border-color: #3B82F6;
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .menu-title {
            font-weight: 600;
            font-size: 1.05rem;
            color: #1E293B;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }
        .menu-price {
            color: #10B981;
            font-weight: 700;
            font-size: 1rem;
        }
        .menu-icon-btn {
            color: #94A3B8;
            font-size: 1.25rem;
            transition: color 0.2s;
        }
        .menu-card:hover .menu-icon-btn {
            color: #3B82F6;
        }

        /* Cart */
        .table-cart {
            margin-bottom: 0;
            color: #334155;
        }
        .table-cart th {
            font-weight: 500;
            color: #64748B;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #E2E8F0;
            padding: 1rem 0.5rem;
        }
        .table-cart td {
            padding: 1rem 0.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
        }
        .qty-input {
            width: 60px;
            text-align: center;
            border-radius: 0.5rem;
            border: 1px solid #CBD5E1;
            font-weight: 500;
        }
        .qty-input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .total-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .btn-pay {
            border-radius: 0.75rem;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            background-color: #0F172A;
            color: white;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-pay:hover:not(:disabled) {
            background-color: #1E293B;
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .btn-pay:disabled {
            background-color: #CBD5E1;
            cursor: not-allowed;
            color: #94A3B8;
        }
        .btn-remove {
            color: #EF4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
            border-radius: 0.5rem;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-remove:hover {
            background: #EF4444;
            color: white;
        }
        
        .empty-state {
            opacity: 0.7;
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="app-navbar shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#" class="navbar-brand-custom">
                <i class="bi bi-shop text-primary h4 mb-0"></i> 
                Kantin<span class="text-primary">Online</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-primary btn-sm rounded-pill fw-medium px-3" data-bs-toggle="modal" data-bs-target="#modalPesananSaya">
                    <i class="bi bi-receipt align-middle me-1"></i> Pesanan Saya 
                    @if(count($myOrders) > 0)
                        <span class="badge bg-danger rounded-circle ms-1">{{ count($myOrders) }}</span>
                    @endif
                </button>
                <div class="small text-muted fw-medium border rounded-pill px-3 py-1 bg-light d-none d-sm-block">
                    <i class="bi bi-record-circle-fill text-success me-1"></i> Sistem Terbuka
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row g-4">

            {{-- ============ KOLOM KIRI: VENDOR & MENU ============ --}}
            <div class="col-lg-7">
                
                {{-- Vendor Selector --}}
                <div class="card-custom mb-4">
                    <div class="card-body-custom">
                        <label class="form-label form-label-custom">Pilih Stand Makanan</label>
                        <select id="select-vendor" class="form-select form-select-custom" onchange="getMenu(this.value)">
                            <option value="0">— Pilih Vendor —</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Menu List --}}
                <div class="card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center bg-white">
                        <span><i class="bi bi-grid-fill text-primary me-2"></i>Katalog Menu</span>
                    </div>
                    <div class="menu-area-wrapper" id="menu-area">
                        <div class="text-center py-5 empty-state">
                            <i class="bi bi-ui-radios-grid display-3 text-secondary mb-3 d-block"></i>
                            <h5 class="fw-semibold text-secondary">Belum Ada Vendor Dipilih</h5>
                            <p class="text-muted small">Pilih nama stand makanan di atas untuk melihat hidangan.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ KOLOM KANAN: KERANJANG (POS) ============ --}}
            <div class="col-lg-5">
                <div class="card-custom h-100 d-flex flex-column">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bag-check-fill text-primary me-2"></i>Daftar Pesanan</span>
                        <span class="badge bg-dark rounded-pill py-2 px-3 fw-medium shadow-sm" id="cart-count">0 Item</span>
                    </div>
                    
                    <div class="card-body-custom flex-grow-1 overflow-auto" style="max-height: 500px;">
                        <table class="table table-cart table-borderless w-100">
                            <thead>
                                <tr>
                                    <th width="45%">Item</th>
                                    <th width="20%" class="text-center">Jml</th>
                                    <th width="25%" class="text-end">Sub</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-cart">
                                {{-- Empty State --}}
                                <tr>
                                    <td colspan="4" class="text-center py-5 empty-state">
                                        <i class="bi bi-bag-x display-4 text-secondary mb-3 d-block"></i>
                                        <h6 class="fw-semibold text-secondary">Keranjang Masih Kosong</h6>
                                        <p class="text-muted small mb-0">Klik pada menu untuk menambah pesanan.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body-custom border-top pt-3 bg-white">
                        <div class="total-box d-flex justify-content-between align-items-center mb-4">
                            <span class="text-secondary fw-semibold">Total Tagihan</span>
                            <span class="fs-4 fw-bold text-dark">Rp <span id="total-harga">0</span></span>
                        </div>
                        <button id="btn-bayar" class="btn btn-pay w-100 shadow-sm" disabled onclick="checkout()">
                            <i class="bi bi-credit-card-2-front me-2"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>

</div>

    {{-- MODAL PESANAN SAYA --}}
    <div class="modal fade" id="modalPesananSaya" tabindex="-1" aria-labelledby="modalPesananSayaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-header border-bottom-0 bg-light" style="border-radius: 1rem 1rem 0 0;">
                    <h5 class="modal-title fw-bold text-dark" id="modalPesananSayaLabel"><i class="bi bi-clock-history text-primary me-2 align-middle"></i>Riwayat Pesanan Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    @if(count($myOrders) == 0)
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-secondary" style="font-size: 4rem; opacity: 0.5;"></i>
                            <h5 class="mt-3 text-muted fw-semibold">Belum Ada Pesanan</h5>
                            <p class="text-muted small mb-0">Pesanan yang Anda buat di perangkat ini akan muncul di sini.</p>
                        </div>
                    @else
                        <div class="accordion" id="accordionOrders">
                            @foreach($myOrders as $idx => $order)
                                <div class="accordion-item mb-3 border rounded shadow-sm overflow-hidden">
                                    <h2 class="accordion-header" id="heading{{ $idx }}">
                                        <button class="accordion-button {{ $idx != 0 ? 'collapsed' : '' }} bg-light fw-semibold pe-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <div>
                                                    <span class="text-dark d-block" style="font-size: 1.05rem;">ID: {{ $order->nama }}</span>
                                                    <div class="text-muted small fw-normal">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
                                                </div>
                                                <div class="text-end">
                                                    @if($order->status_bayar == 1)
                                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                                                    @elseif($order->status_bayar == 2)
                                                        <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i>Batal</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2" id="badge-status-{{ $order->id }}"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $idx }}" class="accordion-collapse collapse {{ $idx == 0 ? 'show' : '' }}" data-bs-parent="#accordionOrders">
                                        <div class="accordion-body p-3 bg-white">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tbody>
                                                        @foreach($order->detail_pesanan as $detail)
                                                            <tr>
                                                                <td class="fw-medium text-dark py-2">
                                                                    {{ $detail->menu->nama_menu ?? 'Menu Dihapus' }}
                                                                    @if(isset($detail->menu->vendor))
                                                                        <div class="text-muted small"><i class="bi bi-shop me-1"></i>{{ $detail->menu->vendor->nama_vendor }}</div>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center py-2 text-muted fw-semibold">{{ $detail->jumlah }}x</td>
                                                                <td class="text-end fw-semibold text-dark py-2">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="border-top">
                                                        <tr>
                                                            <td colspan="2" class="text-end fw-bold pt-3 pb-0 text-secondary">Total Pembayaran</td>
                                                            <td class="text-end fw-bold text-primary pt-3 pb-0 fs-5">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @if($order->status_bayar == 0)
                                                        <tr>
                                                            <td colspan="3" class="text-end pt-2 pb-0">
                                                                <button
                                                                    id="btn-cek-{{ $order->id }}"
                                                                    class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                                                    onclick="cekStatusPesanan({{ $order->id }})"
                                                                >
                                                                    <i class="bi bi-arrow-clockwise me-1"></i>Perbarui Status
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                  <div class="modal-footer bg-light border-top-0" style="border-radius: 0 0 1rem 1rem;">
                      @if(count($myOrders) > 0)
                          <button type="button" class="btn btn-outline-danger me-auto fw-medium rounded-pill px-4" onclick="hapusRiwayat()">
                              <i class="bi bi-trash3 me-1"></i>Hapus Riwayat
                          </button>
                      @endif
                      <button type="button" class="btn btn-secondary fw-medium rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                  </div>
              </div>
          </div>
      </div>

    {{-- JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Script Sandbox Midtrans -->
    <script
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"
    ></script>

    <script>
    let cart = [];

    function getMenu(vendorId) {
        if (vendorId === '0') {
            let emptyHtml = `
                <div class="text-center py-5 empty-state">
                    <i class="bi bi-ui-radios-grid display-3 text-secondary mb-3 d-block"></i>
                    <h5 class="fw-semibold text-secondary">Belum Ada Vendor Dipilih</h5>
                    <p class="text-muted small">Pilih nama stand makanan di atas untuk melihat hidangan.</p>
                </div>
            `;
            $('#menu-area').html(emptyHtml);
            return;
        }

        $('#menu-area').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-medium">Memuat hidangan lezat...</p></div>');

        $.ajax({
            method: 'GET',
            url: '/menu/' + vendorId,
            success: function(response) {
                if (response.status === 'success') {
                    let html = '';

                    if (response.data.length === 0) {
                        html = `
                            <div class="text-center py-5 empty-state">
                                <i class="bi bi-cup-hot display-3 text-secondary mb-3 d-block"></i>
                                <h5 class="fw-semibold text-secondary">Menu Kosong</h5>
                                <p class="text-muted small">Stand ini belum menambahkan menu apapun.</p>
                            </div>
                        `;
                    } else {
                        html = '<div class="row g-3">';
                        response.data.forEach(function(menu) {
                            html += `
                                <div class="col-md-6 col-sm-6">
                                    <div class="menu-card d-flex flex-column p-3" onclick="tambahKeCart(${menu.id}, '${menu.nama_menu}', ${menu.harga})">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="menu-title">${menu.nama_menu}</h6>
                                            <div class="menu-icon-btn"><i class="bi bi-plus-circle-fill"></i></div>
                                        </div>
                                        <div class="mt-auto">
                                            <span class="menu-price">Rp ${Number(menu.harga).toLocaleString('id-ID')}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }

                    $('#menu-area').html(html);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                $('#menu-area').html('<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle display-4 mb-3 d-block"></i>Gagal memuat menu. Coba lagi.</div>');
            }
        });
    }

    function tambahKeCart(menuId, namaMenu, harga) {
        let found = cart.find(item => item.menu_id === menuId);

        if (found) {
            found.jumlah++;
            found.subtotal = found.jumlah * found.harga;
        } else {
            cart.push({
                menu_id: menuId,
                nama: namaMenu,
                harga: harga,
                jumlah: 1,
                subtotal: harga
            });
        }
        renderCart();
    }

    function renderCart() {
        let html = '';
        let total = 0;

        if (cart.length === 0) {
            html = `
                <tr>
                    <td colspan="4" class="text-center py-5 empty-state">
                        <i class="bi bi-bag-x display-4 text-secondary mb-3 d-block"></i>
                        <h6 class="fw-semibold text-secondary">Keranjang Masih Kosong</h6>
                        <p class="text-muted small mb-0">Klik pada menu untuk menambah pesanan.</p>
                    </td>
                </tr>
            `;
            $('#btn-bayar').prop('disabled', true);
        } else {
            cart.forEach(function(item, index) {
                total += item.subtotal;
                html += `
                    <tr>
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">${item.nama}</div>
                            <div class="text-muted small">Rp ${Number(item.harga).toLocaleString('id-ID')}</div>
                        </td>
                        <td class="text-center px-0">
                            <input type="number" class="form-control form-control-sm qty-input mx-auto"
                                   value="${item.jumlah}" min="1"
                                   onchange="updateQty(${index}, this.value)">
                        </td>
                        <td class="text-end fw-semibold text-dark">
                            ${Number(item.subtotal).toLocaleString('id-ID')}
                        </td>
                        <td class="text-end px-0">
                            <button class="btn-remove shadow-sm" onclick="hapusItem(${index})" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#btn-bayar').prop('disabled', false);
        }

        $('#tbody-cart').html(html);
        $('#total-harga').text(Number(total).toLocaleString('id-ID'));
        $('#cart-count').text(cart.length + ' Item');
    }

    function updateQty(index, newQty) {
        newQty = parseInt(newQty);
        if (newQty < 1) newQty = 1;

        cart[index].jumlah = newQty;
        cart[index].subtotal = cart[index].harga * newQty;
        renderCart();
    }

    function hapusItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function prosesBayar() {
        let total = cart.reduce((sum, item) => sum + item.subtotal, 0);

        // Loading state
        let btn = $('#btn-bayar');
        let originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...');

        $.ajax({
            method: 'POST',
            url: '/bayar',
            data: {
                _token: '{{ csrf_token() }}',
                items: cart,
                total: total
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pesanan Berhasil!',
                        html: `
                            <div class="text-start mt-3 p-3 bg-light rounded border">
                                <div class="mb-2">📋 Nama: <b>${response.data.nama}</b></div>
                                <div class="mb-2">🏷️ ID Pesanan: <b>#${response.data.id}</b></div>
                                <hr class="my-2">
                                <div class="fs-5">💰 Total: <b class="text-success">Rp ${Number(total).toLocaleString('id-ID')}</b></div>
                            </div>
                            <div class="mt-3 text-muted small">Tunjukkan pesanan ini ke kasir/vendor.</div>
                        `,
                        confirmButtonColor: '#0F172A',
                        confirmButtonText: 'Selesai'
                    });

                    // Reset
                    cart = [];
                    renderCart();
                    $('#select-vendor').val('0');
                    getMenu('0'); // trigger empty vendor view
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Transaksi Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan pesanan.',
                    confirmButtonColor: '#0F172A'
                });
            },
            complete: function() {
                if(cart.length > 0) btn.prop('disabled', false);
                btn.html(originalText);
            }
        });
    }

    function checkout() {
        let total = cart.reduce((sum, item) => sum + item.subtotal, 0);

        // Loading state
        let btn = $('#btn-bayar');
        let originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...');

        // Siapkan data yang mau dikirim JSON
        const dataKeranjang = {
            total: total,
            items: cart
        };

        fetch("/bayar", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            body: JSON.stringify(dataKeranjang),
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                // JIKA SUKSES, PANGGIL POP-UP MIDTRANS PAKAI TOKEN-NYA
                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        
                        /* =================================================================
                           CATATAN UNTUK PRESENTASI / JAWABAN DOSEN:
                           -----------------------------------------------------------------
                           "Pak/Bu, aslinya jika web kita sudah online, server Midtrans yang 
                           langsung memanggil route backend callback kita secara otomatis."

                           "Namun karena kita jalankan di Localhost (offline), Midtrans tidak 
                           bisa menembus masuk ke komputer kita. Jadi kami mem-bypass 
                           sistemnya lewat JavaScript."

                           "Begitu pembayaran di popup sukses (onSuccess), Javascript ini 
                           langsung mengirim HTTP Request (fetch) 'Palsu' ke backend kita 
                           menyatakan pesanan 'settlement' (Lunas)."
                           ================================================================= */
                        fetch("/midtrans/callback", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                transaction_status: "settlement", // Paksa status lunas
                                order_id: result.order_id // Kirim ID order dari Midtrans
                            }),
                        }).then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Pesanan sudah masuk ke Dashboard Vendor.',
                                confirmButtonColor: '#0F172A'
                            }).then(() => {
                                window.location.reload(); // Refresh layar setelah lunas
                            });
                        });
                    },
                    onPending: function (result) {
                        Swal.fire('Info', 'Menunggu pembayaran Anda diselesaikan.', 'info');
                    },
                    onError: function (result) {
                        Swal.fire('Gagal', 'Pembayaran gagal, silakan coba lagi.', 'error');
                        btn.prop('disabled', false).html(originalText);
                    },
                    onClose: function () {
                        // Batal bayar ketika ditutup (Trik Localhost)
                        fetch("/midtrans/callback", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                transaction_status: "cancel", 
                                order_id: "ORDER-" + data.pesanan_id // Mengirim pesanan_id agar dibatalkan
                            }),
                        }).then(() => {
                            Swal.fire('Batal', 'Anda menutup layar. Pesanan otomatis dibatalkan.', 'warning').then(() => {
                                window.location.reload(); // Refresh layar
                            });
                        });
                        btn.prop('disabled', false).html(originalText);
                    },
                });
            } else {
                Swal.fire('Error', data.message || 'Terjadi kesalahan internal', 'error');
                btn.prop('disabled', false).html(originalText);
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
            btn.prop('disabled', false).html(originalText);
        });
    }

      function hapusRiwayat() {
          Swal.fire({
              title: 'Hapus Riwayat?',
              text: "Riwayat pesanan yang belum lunas atau selesai tidak dapat dilihat kembali di perangkat ini.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#dc3545',
              cancelButtonColor: '#6c757d',
              confirmButtonText: 'Ya, Hapus',
              cancelButtonText: 'Batal'
          }).then((result) => {
              if (result.isConfirmed) {
                  fetch("/hapus-riwayat", {
                      method: "POST",
                      headers: {
                          "Content-Type": "application/json",
                          "X-CSRF-TOKEN": "{{ csrf_token() }}",
                          "Accept": "application/json"
                      }
                  })
                  .then(response => {
                      if(!response.ok) throw new Error("Gagal mengambil data dari server");
                      return response.json();
                  })
                  .then(data => {
                      if(data.status === 'success') {
                          Swal.fire(
                              'Terhapus!',
                              'Riwayat pesanan berhasil dihapus.',
                              'success'
                          ).then(() => {
                              window.location.reload();
                          });
                      } else {
                          Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                      }
                  })
                  .catch(error => {
                      console.error("Error Hapus:", error);
                      Swal.fire('Error', 'Terjadi kesalahan koneksi atau URL endpoint', 'error');
                  });
              }
          });
      }

      function cekStatusPesanan(orderId) {
          const btn = document.getElementById('btn-cek-' + orderId);
          const badge = document.getElementById('badge-status-' + orderId);
          const originalHtml = btn.innerHTML;

          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Memeriksa...';

          fetch(`/pesanan/${orderId}/status`, {
              headers: { 'Accept': 'application/json' }
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  if (data.status_bayar == 1) {
                      // Lunas — perbarui badge dan sembunyikan tombol
                      if (badge) {
                          badge.className = 'badge bg-success rounded-pill px-3 py-2';
                          badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Lunas';
                      }
                      btn.style.display = 'none';
                      Swal.fire({
                          icon: 'success',
                          title: 'Pembayaran Terkonfirmasi!',
                          text: 'Pesanan #' + orderId + ' sudah berstatus Lunas.',
                          confirmButtonColor: '#0F172A'
                      });
                  } else if (data.status_bayar == 2) {
                      // Dibatalkan
                      if (badge) {
                          badge.className = 'badge bg-danger rounded-pill px-3 py-2';
                          badge.innerHTML = '<i class="bi bi-x-circle me-1"></i>Batal';
                      }
                      btn.style.display = 'none';
                      Swal.fire('Info', 'Pesanan ini telah dibatalkan atau kadaluarsa.', 'warning');
                  } else {
                      // Masih pending
                      Swal.fire('Menunggu', 'Pembayaran untuk pesanan ini masih belum diterima. Silakan selesaikan pembayaran Anda.', 'info');
                      btn.disabled = false;
                      btn.innerHTML = originalHtml;
                  }
              } else {
                  Swal.fire('Error', 'Gagal memeriksa status pesanan.', 'error');
                  btn.disabled = false;
                  btn.innerHTML = originalHtml;
              }
          })
          .catch(err => {
              console.error(err);
              Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
              btn.disabled = false;
              btn.innerHTML = originalHtml;
          });
      }
    </script>
</body>
</html>
