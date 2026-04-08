@extends('layouts.main')

@section('content')

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Atur Posisi Cetak Label</h4>

        <form action="{{ route('label.generate') }}" method="POST">
            @csrf

            {{-- Simpan barang yang dipilih --}}
            @foreach($barangs as $barang)
                <input type="hidden" name="barang_ids[]" 
                       value="{{ $barang->id_barang }}">
            @endforeach

            <div class="form-group mb-3">
                <label>Posisi X (Kolom 1 - 5)</label>
                <input type="number" name="x" class="form-control"
                       min="1" max="5" required>
            </div>

            <div class="form-group mb-3">
                <label>Posisi Y (Baris 1 - 8)</label>
                <input type="number" name="y" class="form-control"
                       min="1" max="8" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Generate PDF
            </button>
        </form>
    </div>
</div>

@endsection