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
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
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
                                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>
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
        window.customerConfig = {
            csrfToken: @json(csrf_token()),
            endpoints: {
                menuBase: @json(url('/menu')),
                bayar: @json(url('/bayar')),
                midtransCallback: @json(url('/midtrans/callback')),
                hapusRiwayat: @json(url('/hapus-riwayat')),
            }
        };
    </script>
    <script src="{{ asset('js/customer.js') }}"></script>
</body>
</html>
