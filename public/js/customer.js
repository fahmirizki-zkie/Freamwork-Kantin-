let cart = [];

const customerConfig = window.customerConfig || {};
const csrfToken = customerConfig.csrfToken || "";
const endpoints = customerConfig.endpoints || {};

const menuBaseUrl = endpoints.menuBase || "/menu";
const bayarUrl = endpoints.bayar || "/bayar";
const midtransCallbackUrl = endpoints.midtransCallback || "/midtrans/callback";
const hapusRiwayatUrl = endpoints.hapusRiwayat || "/hapus-riwayat";

function getMenu(vendorId) {
    if (vendorId === "0") {
        let emptyHtml = `
            <div class="text-center py-5 empty-state">
                <i class="bi bi-ui-radios-grid display-3 text-secondary mb-3 d-block"></i>
                <h5 class="fw-semibold text-secondary">Belum Ada Vendor Dipilih</h5>
                <p class="text-muted small">Pilih nama stand makanan di atas untuk melihat hidangan.</p>
            </div>
        `;
        $("#menu-area").html(emptyHtml);
        return;
    }

    $("#menu-area").html(
        '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-medium">Memuat hidangan lezat...</p></div>',
    );

    $.ajax({
        method: "GET",
        url: menuBaseUrl + "/" + vendorId,
        success: function (response) {
            if (response.status === "success") {
                let html = "";

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
                    response.data.forEach(function (menu) {
                        const imageUrl = menu.path_gambar
                            ? `/${menu.path_gambar}`
                            : null;

                        const imageHtml = imageUrl
                            ? `<img src="${imageUrl}" alt="${menu.nama_menu}" class="menu-thumb">`
                            : `<div class="menu-thumb-placeholder"><i class="bi bi-image"></i></div>`;

                        html += `
                            <div class="col-md-6 col-sm-6">
                                <div class="menu-card d-flex flex-column p-3" onclick="tambahKeCart(${menu.id}, '${menu.nama_menu}', ${menu.harga})">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="menu-title">${menu.nama_menu}</h6>
                                        <div class="menu-icon-btn"><i class="bi bi-plus-circle-fill"></i></div>
                                    </div>
                                    <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                                        <span class="menu-price">Rp ${Number(menu.harga).toLocaleString("id-ID")}</span>
                                        ${imageHtml}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += "</div>";
                }

                $("#menu-area").html(html);
            }
        },
        error: function (xhr) {
            console.log("Error:", xhr);
            $("#menu-area").html(
                '<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle display-4 mb-3 d-block"></i>Gagal memuat menu. Coba lagi.</div>',
            );
        },
    });
}

function tambahKeCart(menuId, namaMenu, harga) {
    let found = cart.find((item) => item.menu_id === menuId);

    if (found) {
        found.jumlah++;
        found.subtotal = found.jumlah * found.harga;
    } else {
        cart.push({
            menu_id: menuId,
            nama: namaMenu,
            harga: harga,
            jumlah: 1,
            subtotal: harga,
        });
    }
    renderCart();
}

function renderCart() {
    let html = "";
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
        $("#btn-bayar").prop("disabled", true);
    } else {
        cart.forEach(function (item, index) {
            total += item.subtotal;
            html += `
                <tr>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">${item.nama}</div>
                        <div class="text-muted small">Rp ${Number(item.harga).toLocaleString("id-ID")}</div>
                    </td>
                    <td class="text-center px-0">
                        <input type="number" class="form-control form-control-sm qty-input mx-auto"
                               value="${item.jumlah}" min="1"
                               onchange="updateQty(${index}, this.value)">
                    </td>
                    <td class="text-end fw-semibold text-dark">
                        ${Number(item.subtotal).toLocaleString("id-ID")}
                    </td>
                    <td class="text-end px-0">
                        <button class="btn-remove shadow-sm" onclick="hapusItem(${index})" title="Hapus">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $("#btn-bayar").prop("disabled", false);
    }

    $("#tbody-cart").html(html);
    $("#total-harga").text(Number(total).toLocaleString("id-ID"));
    $("#cart-count").text(cart.length + " Item");
}

function updateQty(index, newQty) {
    newQty = parseInt(newQty, 10);
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

    let btn = $("#btn-bayar");
    let originalText = btn.html();
    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...',
    );

    $.ajax({
        method: "POST",
        url: bayarUrl,
        data: {
            _token: csrfToken,
            items: cart,
            total: total,
        },
        success: function (response) {
            if (response.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Pesanan Berhasil!",
                    html: `
                        <div class="text-start mt-3 p-3 bg-light rounded border">
                            <div class="mb-2">Nama: <b>${response.data.nama}</b></div>
                            <div class="mb-2">ID Pesanan: <b>#${response.data.id}</b></div>
                            <hr class="my-2">
                            <div class="fs-5">Total: <b class="text-success">Rp ${Number(total).toLocaleString("id-ID")}</b></div>
                        </div>
                        <div class="mt-3 text-muted small">Tunjukkan pesanan ini ke kasir/vendor.</div>
                    `,
                    confirmButtonColor: "#0F172A",
                    confirmButtonText: "Selesai",
                });

                cart = [];
                renderCart();
                $("#select-vendor").val("0");
                getMenu("0");
            }
        },
        error: function (xhr) {
            console.log("Error:", xhr);
            Swal.fire({
                icon: "error",
                title: "Transaksi Gagal!",
                text: "Terjadi kesalahan saat menyimpan pesanan.",
                confirmButtonColor: "#0F172A",
            });
        },
        complete: function () {
            if (cart.length > 0) btn.prop("disabled", false);
            btn.html(originalText);
        },
    });
}

function checkout() {
    let total = cart.reduce((sum, item) => sum + item.subtotal, 0);

    let btn = $("#btn-bayar");
    let originalText = btn.html();
    btn.prop("disabled", true).html(
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...',
    );

    const dataKeranjang = {
        total: total,
        items: cart,
    };

    fetch(bayarUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(dataKeranjang),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        /*
                         * Catatan presentasi:
                         * Saat localhost, Midtrans tidak bisa callback langsung ke mesin lokal.
                         * Karena itu frontend mengirim request ke endpoint callback lokal untuk simulasi status pembayaran.
                         */
                        fetch(midtransCallbackUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                            body: JSON.stringify({
                                transaction_status: "settlement",
                                order_id: result.order_id,
                            }),
                        }).then(() => {
                            Swal.fire({
                                icon: "success",
                                title: "Pembayaran Berhasil!",
                                text: "Pesanan sudah masuk ke Dashboard Vendor.",
                                confirmButtonColor: "#0F172A",
                            }).then(() => {
                                window.location.reload();
                            });
                        });
                    },
                    onPending: function () {
                        Swal.fire(
                            "Info",
                            "Menunggu pembayaran Anda diselesaikan.",
                            "info",
                        );
                    },
                    onError: function () {
                        Swal.fire(
                            "Gagal",
                            "Pembayaran gagal, silakan coba lagi.",
                            "error",
                        );
                        btn.prop("disabled", false).html(originalText);
                    },
                    onClose: function () {
                        fetch(midtransCallbackUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                            },
                            body: JSON.stringify({
                                transaction_status: "cancel",
                                order_id: "ORDER-" + data.pesanan_id,
                            }),
                        }).then(() => {
                            Swal.fire(
                                "Batal",
                                "Anda menutup layar. Pesanan otomatis dibatalkan.",
                                "warning",
                            ).then(() => {
                                window.location.reload();
                            });
                        });
                        btn.prop("disabled", false).html(originalText);
                    },
                });
            } else {
                Swal.fire(
                    "Error",
                    data.message || "Terjadi kesalahan internal",
                    "error",
                );
                btn.prop("disabled", false).html(originalText);
            }
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Error", "Terjadi kesalahan koneksi", "error");
            btn.prop("disabled", false).html(originalText);
        });
}

function hapusRiwayat() {
    Swal.fire({
        title: "Hapus Riwayat?",
        text: "Riwayat pesanan yang belum lunas atau selesai tidak dapat dilihat kembali di perangkat ini.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(hapusRiwayatUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            })
                .then((response) => {
                    if (!response.ok)
                        throw new Error("Gagal mengambil data dari server");
                    return response.json();
                })
                .then((data) => {
                    if (data.status === "success") {
                        Swal.fire(
                            "Terhapus!",
                            "Riwayat pesanan berhasil dihapus.",
                            "success",
                        ).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire(
                            "Gagal",
                            "Terjadi kesalahan sistem.",
                            "error",
                        );
                    }
                })
                .catch((error) => {
                    console.error("Error Hapus:", error);
                    Swal.fire(
                        "Error",
                        "Terjadi kesalahan koneksi atau URL endpoint",
                        "error",
                    );
                });
        }
    });
}
