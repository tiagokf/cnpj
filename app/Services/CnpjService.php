<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CnpjService
{
    private const PROVIDERS = [
        'opencnpj' => [
            'base_url' => 'https://opencnpj.com.br/api/v1.0',
            'requires_auth' => false,
        ],
        'cnpjws' => [
            'base_url' => 'https://api.cnpj.ws/cnpj',
            'requires_auth' => false, // Para versão pública
        ],
        'brasilapi' => [
            'base_url' => 'https://brasilapi.com.br/api/cnpj/v1',
            'requires_auth' => false,
        ],
    ];

    public function getCompanyData(string $cnpj, string $provider = null): array
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (!$this->isValidCnpj($cnpj)) {
            return [
                'success' => false,
                'error' => 'CNPJ inválido',
                'data' => null
            ];
        }

        // Se nenhum provedor for especificado, tenta em ordem de preferência
        $providersToTry = $provider ? [$provider] : ['opencnpj', 'cnpjws', 'brasilapi'];

        foreach ($providersToTry as $providerName) {
            if (!isset(self::PROVIDERS[$providerName])) {
                continue;
            }

            try {
                $result = $this->fetchFromProvider($cnpj, $providerName);
                
                if ($result['success']) {
                    return [
                        'success' => true,
                        'data' => $result['data'],
                        'provider' => $providerName
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Falha ao consultar CNPJ na API {$providerName}: " . $e->getMessage());
                continue; // Tenta o próximo provedor
            }
        }

        return [
            'success' => false,
            'error' => 'Falha ao obter dados do CNPJ em todos os provedores disponíveis',
            'data' => null,
            'provider' => null
        ];
    }

    private function fetchFromProvider(string $cnpj, string $provider): array
    {
        $config = self::PROVIDERS[$provider];
        
        switch ($provider) {
            case 'opencnpj':
                return $this->fetchFromOpenCnpj($cnpj, $config);
            case 'cnpjws':
                return $this->fetchFromCnpjWs($cnpj, $config);
            case 'brasilapi':
                return $this->fetchFromBrasilApi($cnpj, $config);
            default:
                throw new \Exception("Provedor {$provider} não suportado");
        }
    }

    private function fetchFromOpenCnpj(string $cnpj, array $config): array
    {
        $response = Http::timeout(30)
            ->get("{$config['base_url']}/{$cnpj}");

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'Falha na API OpenCNPJ',
                'data' => null
            ];
        }

        $data = $response->json();
        
        // Verifica se a resposta é válida
        if (isset($data['error'])) {
            return [
                'success' => false,
                'error' => $data['error'],
                'data' => null
            ];
        }

        return [
            'success' => true,
            'data' => $this->normalizeData($data, 'opencnpj')
        ];
    }

    private function fetchFromCnpjWs(string $cnpj, array $config): array
    {
        $response = Http::timeout(30)
            ->get("{$config['base_url']}/{$cnpj}");

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'Falha na API CNPJ.WS',
                'data' => null
            ];
        }

        $data = $response->json();
        
        if (isset($data['error'])) {
            return [
                'success' => false,
                'error' => $data['error'],
                'data' => null
            ];
        }

        return [
            'success' => true,
            'data' => $this->normalizeData($data, 'cnpjws')
        ];
    }

    private function fetchFromBrasilApi(string $cnpj, array $config): array
    {
        $response = Http::timeout(30)
            ->get("{$config['base_url']}/{$cnpj}");

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'Falha na API Brasil API',
                'data' => null
            ];
        }

        $data = $response->json();
        
        if (isset($data['message'])) {
            return [
                'success' => false,
                'error' => $data['message'],
                'data' => null
            ];
        }

        return [
            'success' => true,
            'data' => $this->normalizeData($data, 'brasilapi')
        ];
    }

    private function normalizeData(array $data, string $source): array
    {
        $normalized = [];
        
        switch ($source) {
            case 'opencnpj':
                $normalized = [
                    'cnpj' => $data['cnpj'] ?? null,
                    'razao_social' => $data['nome'] ?? $data['razao_social'] ?? null,
                    'nome_fantasia' => $data['fantasia'] ?? $data['nome_fantasia'] ?? null,
                    'situacao' => $data['situacao'] ?? null,
                    'abertura' => $data['abertura'] ?? null,
                    'cnae_principal' => [
                        'codigo' => $data['atividade_principal'][0]['code'] ?? null,
                        'descricao' => $data['atividade_principal'][0]['text'] ?? null,
                    ],
                    'natureza_juridica' => $data['natureza_juridica'] ?? null,
                    'porte' => $data['porte'] ?? null,
                    'logradouro' => $data['logradouro'] ?? null,
                    'numero' => $data['numero'] ?? null,
                    'complemento' => $data['complemento'] ?? null,
                    'bairro' => $data['bairro'] ?? null,
                    'municipio' => $data['municipio'] ?? null,
                    'uf' => $data['uf'] ?? null,
                    'cep' => $data['cep'] ?? null,
                    'telefone' => $data['telefone'] ?? null,
                    'email' => $data['email'] ?? null,
                ];
                break;
                
            case 'cnpjws':
                $normalized = [
                    'cnpj' => $data['cnpj'] ?? null,
                    'razao_social' => $data['company']['name'] ?? $data['razao_social'] ?? null,
                    'nome_fantasia' => $data['company']['fantasia'] ?? $data['nome_fantasia'] ?? null,
                    'situacao' => $data['company']['situacao'] ?? null,
                    'abertura' => $data['company']['abertura'] ?? null,
                    'cnae_principal' => [
                        'codigo' => $data['company']['atividade_principal'][0]['code'] ?? null,
                        'descricao' => $data['company']['atividade_principal'][0]['text'] ?? null,
                    ],
                    'natureza_juridica' => $data['company']['natureza_juridica'] ?? null,
                    'porte' => $data['company']['porte'] ?? null,
                    'logradouro' => $data['company']['logradouro'] ?? null,
                    'numero' => $data['company']['numero'] ?? null,
                    'complemento' => $data['company']['complemento'] ?? null,
                    'bairro' => $data['company']['bairro'] ?? null,
                    'municipio' => $data['company']['municipio'] ?? null,
                    'uf' => $data['company']['uf'] ?? null,
                    'cep' => $data['company']['cep'] ?? null,
                    'telefone' => $data['company']['telefone'] ?? null,
                    'email' => $data['company']['email'] ?? null,
                ];
                break;
                
            case 'brasilapi':
                $normalized = [
                    'cnpj' => $data['cnpj'] ?? null,
                    'razao_social' => $data['razao_social'] ?? null,
                    'nome_fantasia' => $data['nome_fantasia'] ?? null,
                    'situacao' => $data['situacao_cadastral'] ?? null,
                    'abertura' => $data['data_inicio_atividade'] ?? null,
                    'cnae_principal' => [
                        'codigo' => $data['cnae_principal']['codigo'] ?? null,
                        'descricao' => $data['cnae_principal']['descricao'] ?? null,
                    ],
                    'natureza_juridica' => $data['natureza_juridica'] ?? null,
                    'porte' => $data['porte'] ?? null,
                    'logradouro' => $data['logradouro'] ?? null,
                    'numero' => $data['numero'] ?? null,
                    'complemento' => $data['complemento'] ?? null,
                    'bairro' => $data['bairro'] ?? null,
                    'municipio' => $data['municipio'] ?? null,
                    'uf' => $data['uf'] ?? null,
                    'cep' => $data['cep'] ?? null,
                    'telefone' => !empty($data['ddd_telefone_1']) ? $data['ddd_telefone_1'] : (!empty($data['ddd_telefone_2']) ? $data['ddd_telefone_2'] : null),
                    'email' => $data['email'] ?? null,
                ];
                break;
        }

        return $normalized;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Verifica se tem 14 dígitos
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j--;
            $j = ($j == 1) ? 9 : $j;
        }

        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cnpj[12] != $digito1) {
            return false;
        }

        // Validação do segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j--;
            $j = ($j == 1) ? 9 : $j;
        }

        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[13] == $digito2;
    }
}