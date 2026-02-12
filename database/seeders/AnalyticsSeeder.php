<?php

namespace Database\Seeders;

use App\Models\CnpjQuery;
use App\Models\PageVisit;
use Illuminate\Database\Seeder;

class AnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPageVisits();
        $this->seedCnpjQueries();
    }

    private function seedPageVisits(): void
    {
        $routes = [
            ['/', 'home'],
            ['/api/cnpj/11222333000181', null],
            ['/dashboard', 'dashboard'],
            ['/settings/profile', 'settings.profile'],
        ];

        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];
        $platforms = ['Windows', 'macOS', 'Linux', 'Android', 'iOS'];
        $devices = ['desktop', 'mobile', 'tablet'];

        // Localização com pesos realistas
        $locations = [
            ['Brasil', 'SP', 'São Paulo', 25],
            ['Brasil', 'RJ', 'Rio de Janeiro', 15],
            ['Brasil', 'MG', 'Belo Horizonte', 10],
            ['Brasil', 'RS', 'Porto Alegre', 7],
            ['Brasil', 'PR', 'Curitiba', 7],
            ['Brasil', 'BA', 'Salvador', 6],
            ['Brasil', 'PE', 'Recife', 5],
            ['Brasil', 'DF', 'Brasília', 5],
            ['Brasil', 'SC', 'Florianópolis', 4],
            ['Brasil', 'CE', 'Fortaleza', 4],
            ['Brasil', 'GO', 'Goiânia', 3],
            ['Brasil', 'PA', 'Belém', 2],
            ['Brasil', 'SP', 'Campinas', 2],
            ['Brasil', 'RJ', 'Niterói', 1],
            ['Portugal', 'LI', 'Lisboa', 2],
            ['Argentina', 'BA', 'Buenos Aires', 2],
        ];

        // Expandir para weighted random
        $locationPool = [];
        foreach ($locations as $loc) {
            for ($w = 0; $w < $loc[3]; $w++) {
                $locationPool[] = [$loc[0], $loc[1], $loc[2]];
            }
        }

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Edg/120.0.0.0',
        ];

        $records = [];

        for ($day = 90; $day >= 0; $day--) {
            $date = now()->subDays($day);
            $visitsCount = rand(5, 50);

            // Mais visitas em dias de semana
            if ($date->isWeekday()) {
                $visitsCount = (int) ($visitsCount * 1.5);
            }

            for ($i = 0; $i < $visitsCount; $i++) {
                $route = $routes[array_rand($routes)];
                $browserIdx = array_rand($browsers);

                $location = $locationPool[array_rand($locationPool)];

                $records[] = [
                    'url' => 'https://consultar-cnpj.com.br' . $route[0],
                    'route_name' => $route[1],
                    'method' => 'GET',
                    'ip_address' => rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'browser' => $browsers[$browserIdx],
                    'platform' => $platforms[array_rand($platforms)],
                    'device_type' => $devices[array_rand($devices)],
                    'country' => $location[0],
                    'state' => $location[1],
                    'city' => $location[2],
                    'referer' => rand(0, 3) === 0 ? 'https://google.com' : null,
                    'user_id' => null,
                    'visited_at' => $date->copy()->addMinutes(rand(0, 1439)),
                ];
            }

            // Inserir em lotes para performance
            if (count($records) >= 500) {
                PageVisit::insert($records);
                $records = [];
            }
        }

        if (! empty($records)) {
            PageVisit::insert($records);
        }
    }

    private function seedCnpjQueries(): void
    {
        $cnpjs = [
            ['11222333000181', 'EMPRESA DEMO LTDA'],
            ['00000000000191', 'BANCO DO BRASIL SA'],
            ['60746948000112', 'BANCO BRADESCO SA'],
            ['33000167000101', 'PETROLEO BRASILEIRO SA PETROBRAS'],
            ['33592510000154', 'VALE SA'],
            ['02558157000162', 'TELEFONICA BRASIL SA'],
            ['33041260065290', 'GLOBO COMUNICACAO E PARTICIPACOES SA'],
            ['61186680000174', 'B3 SA - BRASIL, BOLSA, BALCAO'],
            ['47960950000121', 'MAGAZINE LUIZA SA'],
            ['76535764000143', 'OI SA'],
            ['04666456000196', 'AMERICANAS SA'],
            ['09346601000125', 'UBER DO BRASIL TECNOLOGIA LTDA'],
            ['13347016000117', 'NUBANK'],
            ['10573521000191', 'IFOOD'],
        ];

        $records = [];

        for ($day = 90; $day >= 0; $day--) {
            $date = now()->subDays($day);
            $queriesCount = rand(2, 20);

            for ($i = 0; $i < $queriesCount; $i++) {
                $cnpjData = $cnpjs[array_rand($cnpjs)];
                $success = rand(1, 100) <= 85;
                $source = rand(1, 100) <= 70 ? 'web' : 'api';

                $records[] = [
                    'cnpj' => $cnpjData[0],
                    'razao_social' => $success ? $cnpjData[1] : null,
                    'source' => $source,
                    'success' => $success,
                    'error_message' => $success ? null : 'API CNPJ-WS retornou status 429',
                    'response_time_ms' => $success ? rand(200, 3000) : rand(100, 500),
                    'ip_address' => rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255),
                    'user_id' => null,
                    'queried_at' => $date->copy()->addMinutes(rand(0, 1439)),
                ];
            }

            if (count($records) >= 500) {
                CnpjQuery::insert($records);
                $records = [];
            }
        }

        if (! empty($records)) {
            CnpjQuery::insert($records);
        }
    }
}
