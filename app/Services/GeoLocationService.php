<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    private const API_URL = 'http://ip-api.com/json/';

    private const UF_MAP = [
        'Acre' => 'AC',
        'Alagoas' => 'AL',
        'Amapá' => 'AP',
        'Amazonas' => 'AM',
        'Bahia' => 'BA',
        'Ceará' => 'CE',
        'Distrito Federal' => 'DF',
        'Espírito Santo' => 'ES',
        'Goiás' => 'GO',
        'Maranhão' => 'MA',
        'Mato Grosso' => 'MT',
        'Mato Grosso do Sul' => 'MS',
        'Minas Gerais' => 'MG',
        'Pará' => 'PA',
        'Paraíba' => 'PB',
        'Paraná' => 'PR',
        'Pernambuco' => 'PE',
        'Piauí' => 'PI',
        'Rio de Janeiro' => 'RJ',
        'Rio Grande do Norte' => 'RN',
        'Rio Grande do Sul' => 'RS',
        'Rondônia' => 'RO',
        'Roraima' => 'RR',
        'Santa Catarina' => 'SC',
        'São Paulo' => 'SP',
        'Sergipe' => 'SE',
        'Tocantins' => 'TO',
    ];

    public function resolve(string $ip): ?array
    {
        if ($this->isPrivateIp($ip)) {
            return null;
        }

        try {
            $response = Http::timeout(5)
                ->get(self::API_URL . $ip, [
                    'fields' => 'status,country,regionName,city',
                    'lang' => 'pt-BR',
                ]);

            $data = $response->json();

            if (($data['status'] ?? '') !== 'success') {
                return null;
            }

            return [
                'country' => $data['country'] ?? null,
                'state' => $this->extractUf($data['regionName'] ?? ''),
                'city' => $data['city'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('GeoLocation: falha ao resolver IP', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function isPrivateIp(string $ip): bool
    {
        return ! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    private function extractUf(string $regionName): ?string
    {
        return self::UF_MAP[$regionName] ?? mb_substr($regionName, 0, 2);
    }
}
