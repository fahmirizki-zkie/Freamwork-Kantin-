@extends('layouts.main')

@section('content')
<div class="row">
    <!-- KOLOM STATISTIK (CHART) -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">📈 Grafik Penjualan (7 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                <!-- Canvas Chart.js -->
                <canvas id="penjualanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- KOLOM PESANAN LUNAS -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">✔️ Item Dipesan (Sudah Lunas)</h5>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pembeli</th>
                            <th>Menu</th>
                            <th>Qty</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesananMasuk as $item)
                            <tr>
                                <td>{{ $item->pesanan->nama }} <br> <small class="text-muted">#{{ $item->pesanan->id }}</small></td>
                                <td>{{ $item->menu->nama_menu }}</td>
                                <td>{{ $item->jumlah }}x</td>
                                <td class="text-success fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada pesanan lunas untuk Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Memanggil API Grafik lewat AJAX native (Fetch API)
    fetch("{{ route('vendor.chart') }}")
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('penjualanChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // Coba ubah 'bar' menjadi 'line' kalau mau tipe garis!
                data: {
                    labels: data.labels, // Tanggal (Sumbu X)
                    datasets: [{
                        label: 'Total Pendapatan (Rp)',
                        data: data.data, // Pemasukan (Sumbu Y)
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
</script>
@endpush
