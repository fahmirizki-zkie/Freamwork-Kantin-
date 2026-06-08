<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    private const BASE_RAW_URL = 'https://raw.githubusercontent.com/guzfirdaus/Wilayah-Administrasi-Indonesia/master/csv';
    private const BASE_CDN_URL = 'https://cdn.jsdelivr.net/gh/guzfirdaus/Wilayah-Administrasi-Indonesia@master/csv';

    // Tampilkan halaman utama
    public function index()
    {
        $provinsi = collect($this->fetchCsvRows(self::BASE_RAW_URL . '/provinces.csv'))
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return view('wilayah.index', compact('provinsi'));
    }

    // Ambil kota berdasarkan province_id
    // Dipanggil AJAX saat provinsi dipilih
    public function getKota($province_id)
    {
        $kota = collect($this->fetchCsvRows(self::BASE_RAW_URL . '/regencies.csv'))
            ->where('province_id', $province_id)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'province_id' => $row['province_id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $kota
        ]);
    }

    // Ambil kecamatan berdasarkan regency_id
    // Dipanggil AJAX saat kota dipilih
    public function getKecamatan($regency_id)
    {
        $kecamatan = collect($this->fetchCsvRows(self::BASE_RAW_URL . '/districts.csv'))
            ->where('regency_id', $regency_id)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'regency_id' => $row['regency_id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $kecamatan
        ]);
    }

    // Ambil kelurahan berdasarkan district_id
    // Dipanggil AJAX saat kecamatan dipilih
    public function getKelurahan($district_id)
    {
        $kelurahan = collect($this->fetchCsvRows(self::BASE_RAW_URL . '/villages.csv'))
            ->where('district_id', $district_id)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'district_id' => $row['district_id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $kelurahan
        ]);
    }

    private function fetchCsvRows(string $url): array
    {
        $cacheKey = 'wilayah_csv_' . md5($url);
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $fallbackUrl = str_replace(self::BASE_RAW_URL, self::BASE_CDN_URL, $url);

        foreach ([$url, $fallbackUrl] as $source) {
            $response = Http::timeout(45)->get($source);

            if (!$response->successful()) {
                continue;
            }

            $content = trim($response->body());

            if ($content === '') {
                continue;
            }

            $lines = preg_split('/\r\n|\r|\n/', $content);

            if (!$lines || count($lines) < 2) {
                continue;
            }

            $headerLine = array_shift($lines);
            $delimiter = str_contains($headerLine, ';') ? ';' : ',';
            $headers = str_getcsv($headerLine, $delimiter);
            $rows = [];

            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }

                $values = str_getcsv($line, $delimiter);

                if (count($values) !== count($headers)) {
                    continue;
                }

                $rows[] = array_combine($headers, $values);
            }

            if (!empty($rows)) {
                Cache::put($cacheKey, $rows, now()->addHours(12));
                return $rows;
            }
        }

        return is_array($cached) ? $cached : [];
    }
}
