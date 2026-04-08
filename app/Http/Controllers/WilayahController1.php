<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    private const BASE_RAW_URL = 'https://raw.githubusercontent.com/guzfirdaus/Wilayah-Administrasi-Indonesia/master/csv';
    private const BASE_CDN_URL = 'https://cdn.jsdelivr.net/gh/guzfirdaus/Wilayah-Administrasi-Indonesia@master/csv';

    public function index()
    {
        return view('wilayah.index');
    }

    public function provinces(): JsonResponse
    {
        $rows = $this->fetchCsvRows(self::BASE_RAW_URL . '/provinces.csv');

        $data = collect($rows)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json($data);
    }

    public function regencies(string $provinceId): JsonResponse
    {
        $rows = $this->fetchCsvRows(self::BASE_RAW_URL . '/regencies.csv');

        $data = collect($rows)
            ->where('province_id', $provinceId)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'province_id' => $row['province_id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json($data);
    }

    public function districts(string $regencyId): JsonResponse
    {
        $rows = $this->fetchCsvRows(self::BASE_RAW_URL . '/districts.csv');

        $data = collect($rows)
            ->where('regency_id', $regencyId)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'regency_id' => $row['regency_id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json($data);
    }

    public function villages(string $districtId): JsonResponse
    {
        $rows = $this->fetchCsvRows(self::BASE_RAW_URL . '/villages.csv');

        $data = collect($rows)
            ->where('district_id', $districtId)
            ->map(fn ($row) => [
                'id' => $row['id'] ?? null,
                'district_id' => $row['district_id'] ?? null,
                'name' => $row['name'] ?? null,
            ])
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['name']))
            ->values();

        return response()->json($data);
    }

    private function fetchCsvRows(string $url): array
    {
        $cacheKey = 'wilayah_csv_' . md5($url);
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $fallbackUrl = str_replace(self::BASE_RAW_URL, self::BASE_CDN_URL, $url);
        $sources = [$url, $fallbackUrl];

        foreach ($sources as $source) {
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
