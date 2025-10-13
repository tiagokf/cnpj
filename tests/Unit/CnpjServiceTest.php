<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CnpjService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class CnpjServiceTest extends TestCase
{
    public function test_consulta_cnpj_com_dados_validos()
    {
        $service = new CnpjService();
        
        // Simula resposta da API Brasil
        Http::fake([
            'https://brasilapi.com.br/api/cnpj/v1/12345678000195' => Http::response([
                'cnpj' => '12345678000195',
                'nome' => 'EMPRESA EXEMPLO LTDA',
                'fantasia' => 'EMPRESA EXEMPLO',
                'situacao' => 'ATIVA',
                'logradouro' => 'RUA EXEMPLO',
                'numero' => '123',
                'bairro' => 'CENTRO',
                'municipio' => 'SAO PAULO',
                'uf' => 'SP',
                'cep' => '01001000',
                'data_inicio_atividade' => '2010-01-01',
                'cnae_fiscal' => '6204000',
                'cnae_fiscal_descricao' => 'Desenvolvimento e licenciamento de programas de computador',
                'porte' => 'DEMAIS',
                'natureza_juridica' => '2062 - SOCIEDADE EMPRESÁRIA LIMITADA',
                'qsa' => [
                    [
                        'nome' => 'FULANO DE TAL',
                        'cpf' => '12345678901',
                        'qualificacao' => '49-Sócio-Administrador',
                        'data_entrada_sociedade' => '2010-01-01',
                        'tipo' => 'Física'
                    ]
                ]
            ], 200)
        ]);

        $result = $service->consultaCnpj('12.345.678/0001-95');

        $this->assertTrue($result['sucesso']);
        $this->assertEquals('brasilapi', $result['origem']);
        $this->assertArrayHasKey('dados', $result);
        $this->assertEquals('12345678000195', $result['dados']['cnpj']);
    }
    
    public function test_consulta_cnpj_com_dados_invalidos()
    {
        $service = new CnpjService();
        
        // Simula resposta de erro da API
        Http::fake([
            'https://brasilapi.com.br/api/cnpj/v1/00000000000000' => Http::response(['message' => 'CNPJ não encontrado'], 404)
        ]);

        $result = $service->consultaCnpj('00.000.000/0000-00');

        $this->assertFalse($result['sucesso']);
        $this->assertNull($result['origem']);
        $this->assertNull($result['dados']);
    }
    
    public function test_consulta_cnpj_formato_invalido()
    {
        $service = new CnpjService();
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('CNPJ deve ter 14 dígitos');
        
        $service->consultaCnpj('12345');
    }
}