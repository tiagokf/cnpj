<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CnpjService
{
    private const BASE_URL = 'https://publica.cnpj.ws/cnpj';
    private const COMMERCIAL_URL = 'https://comercial.cnpj.ws/cnpj';

    public function getCompanyData(string $cnpj): array
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (!$this->isValidCnpj($cnpj)) {
            return [
                'success' => false,
                'error' => 'CNPJ inválido',
                'data' => null,
            ];
        }

        try {
            $data = $this->fetchFromCnpjWs($cnpj);

            return [
                'success' => true,
                'data' => $this->normalizeData($data),
            ];
        } catch (\Exception $e) {
            Log::error("Falha ao consultar CNPJ na API CNPJ-WS: {$e->getMessage()}");

            return [
                'success' => false,
                'error' => 'Falha ao obter dados do CNPJ: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    public function consultaCnpj(string $cnpj): array
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (!$this->isValidCnpj($cnpj)) {
            return [
                'sucesso' => false,
                'mensagem' => 'CNPJ inválido',
                'dados' => null,
            ];
        }

        try {
            $data = $this->fetchFromCnpjWs($cnpj);

            return [
                'sucesso' => true,
                'dados' => $this->normalizeData($data),
                'mensagem' => null,
            ];
        } catch (\Exception $e) {
            Log::error("Falha ao consultar CNPJ na API CNPJ-WS: {$e->getMessage()}");

            return [
                'sucesso' => false,
                'mensagem' => 'Falha ao obter dados do CNPJ: ' . $e->getMessage(),
                'dados' => null,
            ];
        }
    }

    private function fetchFromCnpjWs(string $cnpj): array
    {
        $token = config('services.cnpjws.token');

        if ($token) {
            $url = self::COMMERCIAL_URL . '/' . $cnpj . '?token=' . $token;
        } else {
            $url = self::BASE_URL . '/' . $cnpj;
        }

        $response = Http::timeout(30)
            ->withUserAgent('consultar-cnpj')
            ->get($url);

        if (!$response->successful()) {
            throw new \Exception('API CNPJ-WS retornou status ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['error']) || isset($data['message'])) {
            throw new \Exception($data['error'] ?? $data['message']);
        }

        return $data;
    }

    private function normalizeData(array $data): array
    {
        $estabelecimento = $data['estabelecimento'] ?? [];

        $telefone = null;
        $ddd = $estabelecimento['ddd1'] ?? null;
        $numero = $estabelecimento['telefone1'] ?? null;
        if ($ddd && $numero) {
            $telefone = "({$ddd}) {$numero}";
        }

        $socios = [];
        if (isset($data['socios']) && is_array($data['socios'])) {
            foreach ($data['socios'] as $socio) {
                $socios[] = [
                    'nome' => $socio['nome'] ?? null,
                    'cpf_cnpj_socio' => $socio['cpf_cnpj_socio'] ?? null,
                    'qualificacao_socio' => $socio['qualificacao_socio'] ?? null,
                    'data_entrada' => $socio['data_entrada'] ?? null,
                ];
            }
        }

        $normalized = [
            'cnpj' => $estabelecimento['cnpj'] ?? null,
            'razao_social' => $data['razao_social'] ?? null,
            'nome_fantasia' => $estabelecimento['nome_fantasia'] ?? null,
            'situacao_cadastral' => $estabelecimento['situacao_cadastral'] ?? null,
            'data_inicio_atividade' => $estabelecimento['data_inicio_atividade'] ?? null,
            'cnae_principal' => [
                'codigo' => $estabelecimento['atividade_principal']['id'] ?? null,
                'descricao' => $estabelecimento['atividade_principal']['descricao'] ?? null,
            ],
            'natureza_juridica' => isset($data['natureza_juridica'])
                ? ($data['natureza_juridica']['id'] ?? '') . ' - ' . ($data['natureza_juridica']['descricao'] ?? '')
                : null,
            'porte' => $data['porte']['descricao'] ?? null,
            'capital_social' => $data['capital_social'] ?? null,
            'logradouro' => trim(($estabelecimento['tipo_logradouro'] ?? '') . ' ' . ($estabelecimento['logradouro'] ?? '')),
            'numero' => $estabelecimento['numero'] ?? null,
            'complemento' => $estabelecimento['complemento'] ?? null,
            'bairro' => $estabelecimento['bairro'] ?? null,
            'municipio' => $estabelecimento['cidade']['nome'] ?? null,
            'uf' => $estabelecimento['estado']['sigla'] ?? null,
            'cep' => $estabelecimento['cep'] ?? null,
            'telefone' => $telefone,
            'email' => $estabelecimento['email'] ?? null,
            'inscricao_estadual' => $this->extractInscricaoEstadual($estabelecimento['inscricoes_estaduais'] ?? []),
            'socios' => $socios,
            'simples' => $data['simples'] ?? null,
        ];

        // Compatibilidade com campos antigos
        $normalized['situacao'] = $normalized['situacao_cadastral'];
        $normalized['abertura'] = $normalized['data_inicio_atividade'];

        return $normalized;
    }

    private function extractInscricaoEstadual(array $inscricoes): ?string
    {
        if (empty($inscricoes)) {
            return null;
        }

        // Busca a primeira inscrição ativa
        foreach ($inscricoes as $inscricao) {
            if (isset($inscricao['inscricao_estadual']) &&
                (!isset($inscricao['ativo']) || $inscricao['ativo'] === true)) {
                return $inscricao['inscricao_estadual'];
            }
        }

        // Se não encontrou ativa, retorna a primeira
        return $inscricoes[0]['inscricao_estadual'] ?? null;
    }

    private function isValidCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j--;
            $j = ($j == 1) ? 9 : $j;
        }

        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cnpj[12] != $digito1) {
            return false;
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j--;
            $j = ($j == 1) ? 9 : $j;
        }

        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[13] == $digito2;
    }
}
