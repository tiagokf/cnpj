<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CnpjService;
use Illuminate\Support\Facades\Http;

class CnpjServiceTest extends TestCase
{
    private function fakeCnpjWsResponse(): array
    {
        return [
            'razao_social' => 'BANCO DO BRASIL SA',
            'capital_social' => '120000000000.00',
            'porte' => [
                'id' => 5,
                'descricao' => 'Demais',
            ],
            'natureza_juridica' => [
                'id' => '2038',
                'descricao' => 'Sociedade de Economia Mista',
            ],
            'socios' => [
                [
                    'nome' => 'TARCIANA PAULA GOMES MEDEIROS',
                    'cpf_cnpj_socio' => '***456789**',
                    'qualificacao_socio' => [
                        'id' => 10,
                        'descricao' => 'Diretor',
                    ],
                    'data_entrada' => '2023-01-13',
                ],
            ],
            'simples' => [
                'mei' => false,
                'simples' => false,
            ],
            'estabelecimento' => [
                'cnpj' => '00000000000191',
                'nome_fantasia' => 'DIRECAO GERAL',
                'situacao_cadastral' => 'Ativa',
                'data_inicio_atividade' => '1966-08-01',
                'tipo_logradouro' => 'Quadra',
                'logradouro' => 'SAUN Quadra 5 Lote B',
                'numero' => 'S/N',
                'complemento' => 'ANDAR 1 A 16 SALA 101 A 1601 ED BANCO DO BRASIL',
                'bairro' => 'Asa Norte',
                'cep' => '70040912',
                'ddd1' => '61',
                'telefone1' => '34939002',
                'email' => 'difis@bb.com.br',
                'atividade_principal' => [
                    'id' => '64.22-1-00',
                    'descricao' => 'Bancos múltiplos, com carteira comercial',
                ],
                'estado' => [
                    'id' => 53,
                    'nome' => 'Distrito Federal',
                    'sigla' => 'DF',
                    'ibge_id' => 53,
                ],
                'cidade' => [
                    'id' => 9701,
                    'nome' => 'Brasilia',
                    'ibge_id' => 5300108,
                    'siafi_id' => '9701',
                ],
                'inscricoes_estaduais' => [
                    [
                        'inscricao_estadual' => '0730421900117',
                        'ativo' => true,
                        'estado' => [
                            'id' => 53,
                            'nome' => 'Distrito Federal',
                            'sigla' => 'DF',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_consulta_cnpj_com_dados_validos()
    {
        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response($this->fakeCnpjWsResponse(), 200),
        ]);

        $service = new CnpjService();
        $result = $service->consultaCnpj('00.000.000/0001-91');

        $this->assertTrue($result['sucesso']);
        $this->assertNull($result['mensagem']);

        $dados = $result['dados'];
        $this->assertEquals('00000000000191', $dados['cnpj']);
        $this->assertEquals('BANCO DO BRASIL SA', $dados['razao_social']);
        $this->assertEquals('DIRECAO GERAL', $dados['nome_fantasia']);
        $this->assertEquals('Ativa', $dados['situacao_cadastral']);
        $this->assertEquals('1966-08-01', $dados['data_inicio_atividade']);
        $this->assertEquals('DF', $dados['uf']);
        $this->assertEquals('Brasilia', $dados['municipio']);
        $this->assertEquals('(61) 34939002', $dados['telefone']);
        $this->assertEquals('difis@bb.com.br', $dados['email']);
        $this->assertEquals('0730421900117', $dados['inscricao_estadual']);
        $this->assertStringContainsString('Quadra', $dados['logradouro']);
        $this->assertEquals('64.22-1-00', $dados['cnae_principal']['codigo']);
        $this->assertStringContainsString('2038', $dados['natureza_juridica']);
        $this->assertEquals('Demais', $dados['porte']);
    }

    public function test_consulta_cnpj_retorna_socios()
    {
        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response($this->fakeCnpjWsResponse(), 200),
        ]);

        $service = new CnpjService();
        $result = $service->consultaCnpj('00.000.000/0001-91');

        $this->assertTrue($result['sucesso']);
        $this->assertCount(1, $result['dados']['socios']);

        $socio = $result['dados']['socios'][0];
        $this->assertEquals('TARCIANA PAULA GOMES MEDEIROS', $socio['nome']);
        $this->assertEquals('***456789**', $socio['cpf_cnpj_socio']);
        $this->assertEquals('Diretor', $socio['qualificacao_socio']['descricao']);
        $this->assertEquals('2023-01-13', $socio['data_entrada']);
    }

    public function test_consulta_cnpj_formato_invalido()
    {
        $service = new CnpjService();

        $result = $service->consultaCnpj('12345');

        $this->assertFalse($result['sucesso']);
        $this->assertStringContainsString('CNPJ inválido', $result['mensagem']);
        $this->assertNull($result['dados']);
    }

    public function test_consulta_cnpj_todos_digitos_iguais()
    {
        $service = new CnpjService();

        $result = $service->consultaCnpj('11111111111111');

        $this->assertFalse($result['sucesso']);
        $this->assertStringContainsString('CNPJ inválido', $result['mensagem']);
    }

    public function test_get_company_data_com_dados_validos()
    {
        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response($this->fakeCnpjWsResponse(), 200),
        ]);

        $service = new CnpjService();
        $result = $service->getCompanyData('00000000000191');

        $this->assertTrue($result['success']);
        $this->assertEquals('BANCO DO BRASIL SA', $result['data']['razao_social']);
        $this->assertEquals('0730421900117', $result['data']['inscricao_estadual']);
    }

    public function test_get_company_data_api_retorna_erro()
    {
        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response([], 500),
        ]);

        $service = new CnpjService();
        $result = $service->getCompanyData('00000000000191');

        $this->assertFalse($result['success']);
        $this->assertNull($result['data']);
        $this->assertStringContainsString('status 500', $result['error']);
    }

    public function test_get_company_data_cnpj_invalido()
    {
        $service = new CnpjService();
        $result = $service->getCompanyData('12345');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('CNPJ inválido', $result['error']);
    }

    public function test_usa_api_comercial_quando_token_disponivel()
    {
        config(['services.cnpjws.token' => 'meu-token-123']);

        Http::fake([
            'https://comercial.cnpj.ws/cnpj/00000000000191?token=meu-token-123' => Http::response($this->fakeCnpjWsResponse(), 200),
        ]);

        $service = new CnpjService();
        $result = $service->getCompanyData('00000000000191');

        $this->assertTrue($result['success']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'comercial.cnpj.ws');
        });
    }

    public function test_inscricao_estadual_retorna_ativa()
    {
        $response = $this->fakeCnpjWsResponse();
        $response['estabelecimento']['inscricoes_estaduais'] = [
            ['inscricao_estadual' => '111111', 'ativo' => false],
            ['inscricao_estadual' => '222222', 'ativo' => true],
        ];

        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response($response, 200),
        ]);

        $service = new CnpjService();
        $result = $service->getCompanyData('00000000000191');

        $this->assertEquals('222222', $result['data']['inscricao_estadual']);
    }

    public function test_inscricao_estadual_nula_quando_vazia()
    {
        $response = $this->fakeCnpjWsResponse();
        $response['estabelecimento']['inscricoes_estaduais'] = [];

        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response($response, 200),
        ]);

        $service = new CnpjService();
        $result = $service->getCompanyData('00000000000191');

        $this->assertNull($result['data']['inscricao_estadual']);
    }

    public function test_compatibilidade_campos_antigos()
    {
        Http::fake([
            'https://publica.cnpj.ws/cnpj/00000000000191' => Http::response($this->fakeCnpjWsResponse(), 200),
        ]);

        $service = new CnpjService();
        $result = $service->getCompanyData('00000000000191');

        $this->assertEquals($result['data']['situacao_cadastral'], $result['data']['situacao']);
        $this->assertEquals($result['data']['data_inicio_atividade'], $result['data']['abertura']);
    }
}
