<?php

namespace App\Models;

use Illuminate\Support\Facades\Session;

class M_OngkirApi
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('rajaongkir.api_key', '');
        $this->baseUrl = config('rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
    }

    public function searchDestination(string $keyword, float $lat = 0, float $lng = 0): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'API key belum dikonfigurasi'];
        }

        if ($lat != 0 && $lng != 0) {
            $latR = round($lat, 2);
            $lngR = round($lng, 2);
            $coordKey = 'search_dest_coord_' . $latR . '_' . $lngR;
            $cached = Session::get($coordKey);
            if ($cached && isset($cached['data']) && isset($cached['time']) && (time() - $cached['time'] < 1800)) {
                return $cached['data'];
            }
        }

        $url = $this->baseUrl . '/destination/domestic-destination?search=' . urlencode($keyword) . '&limit=20&offset=0';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER    => ['key: ' . $this->apiKey],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT       => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return ['error' => 'Gagal menghubungi server RajaOngkir'];
        }

        $result = json_decode($response, true);
        if (!$result || !isset($result['data'])) {
            return ['error' => 'Data tidak ditemukan'];
        }

        if ($lat != 0 && $lng != 0) {
            $latR = round($lat, 2);
            $lngR = round($lng, 2);
            $coordKey = 'search_dest_coord_' . $latR . '_' . $lngR;
            Session::put($coordKey, ['data' => $result['data'], 'time' => time()]);
        }

        return $result['data'];
    }

    public function refineAddress(string $desa, string $kota = '', string $provinsi = '', float $lat = 0, float $lng = 0): array
    {
        $norm = function (string $s): string {
            $s = mb_strtolower(trim($s));
            $s = preg_replace('/^(kota|kabupaten|kecamatan|kelurahan|desa|administrasi)\s+/', '', $s);
            $s = preg_replace('/^(kota|kabupaten|kecamatan|kelurahan|desa|administrasi)\s+/', '', $s);
            return preg_replace('/[^a-z0-9]+/', '', $s);
        };

        $desaN = $norm($desa);
        $kotaN = $norm($kota);

        if ($desaN === '' || $kotaN === '') {
            return ['match' => null];
        }

        $keyword = trim($desa . ' ' . $kota);
        $results = $this->searchDestination($keyword, $lat, $lng);

        if (isset($results['error']) || empty($results)) {
            return ['match' => null];
        }

        $candidates = [];
        foreach ($results as $item) {
            $subN  = $norm($item['subdistrict_name'] ?? '');
            $cityN = $norm($item['city_name'] ?? '');
            if ($cityN !== '' && $cityN !== $kotaN) {
                continue;
            }
            if ($subN === $desaN) {
                $candidates[] = $item;
            }
        }

        if (empty($candidates)) {
            foreach ($results as $item) {
                $subN  = $norm($item['subdistrict_name'] ?? '');
                $cityN = $norm($item['city_name'] ?? '');
                if ($cityN !== '' && $cityN !== $kotaN) {
                    continue;
                }
                if ($subN !== '' && (strpos($subN, $desaN) !== false || strpos($desaN, $subN) !== false)) {
                    $candidates[] = $item;
                }
            }
        }

        if (empty($candidates)) {
            return ['match' => null];
        }

        $best = $candidates[0];

        return [
            'match' => [
                'id'        => (int) ($best['id'] ?? 0),
                'desa'      => $best['subdistrict_name'] ?? '',
                'kecamatan' => $best['district_name'] ?? '',
                'kota'      => $best['city_name'] ?? '',
                'provinsi'  => $best['province_name'] ?? '',
            ],
        ];
    }

    public function calculateCost(int $originId, int $destinationId, int $weightGram, string $courier = 'jne:jnt:sicepat'): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'API key belum dikonfigurasi'];
        }

        $cacheKey = 'ongkir_' . $originId . '_' . $destinationId . '_' . $weightGram . '_' . $courier;
        $cached = Session::get($cacheKey);
        if ($cached && isset($cached['data']) && isset($cached['time']) && (time() - $cached['time'] < 1800)) {
            return $cached['data'];
        }

        $url = $this->baseUrl . '/calculate/domestic-cost';

        $postData = http_build_query([
            'origin'      => $originId,
            'destination' => $destinationId,
            'weight'      => $weightGram,
            'courier'     => $courier,
            'price'       => 'lowest',
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'key: ' . $this->apiKey,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT       => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 429) {
            return ['error' => 'Kuota API RajaOngkir harian habis. Silakan coba lagi besok.'];
        }
        if ($httpCode !== 200 || !$response) {
            return ['error' => 'Gagal menghitung ongkos kirim (HTTP ' . $httpCode . ')'];
        }

        $result = json_decode($response, true);
        if (!$result || !isset($result['data'])) {
            return ['error' => $result['meta']['message'] ?? 'Gagal menghitung ongkos kirim'];
        }

        Session::put($cacheKey, ['data' => $result['data'], 'time' => time()]);

        return $result['data'];
    }
}
