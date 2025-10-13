# Integração com APIs de Consulta de CNPJ

Este documento descreve como foram implementadas alternativas à OpenCNPJ no projeto, utilizando as APIs CNPJ.WS e Brasil API.

## APIs Disponíveis

### 1. CNPJ.WS

- **URL Base**: `https://www.cnpj.ws/api/v3/companies/{cnpj}` (paga) ou `https://publica.cnpj.ws/cnpj/{cnpj}` (pública)
- **Autenticação**: Token Bearer (opcional para API pública)
- **Limitações**:
  - API Pública: 3 consultas por minuto e 180 por hora
  - Se ultrapassar 360 consultas por hora, penalização por 1 hora
  - Apenas uso comercial requer plano pago
- **Vantagens**:
  - Dados atualizados frequentemente
  - Retorna dados detalhados do CNPJ
  - Inclui informações sobre sócios

### 2. Brasil API

- **URL Base**: `https://brasilapi.com.br/api/cnpj/v1/{cnpj}`
- **Autenticação**: Não requer
- **Vantagens**:
  - Totalmente gratuita
  - Sem necessidade de autenticação
  - Sem limites rígidos de requisições
- **Limitações**:
  - Pode ter limitações não documentadas de requisições em curto período

## Configuração

### Variáveis de Ambiente

Adicione a seguinte variável ao seu arquivo `.env` para usar a API CNPJ.WS com token:

```env
CNPJWS_TOKEN=seu_token_aqui
```

### Estrutura de Configuração

A configuração está em `config/services.php`:

```php
'cnpjws' => [
    'token' => env('CNPJWS_TOKEN', null),
],

'brasilapi' => [
    // A Brasil API não requer autenticação
],
```

## Uso do Serviço

A classe `App\Services\CnpjService` fornece um método `consultaCnpj` que permite:

1. Consultar usando uma API específica
2. Usar fallback entre APIs
3. Formatar os dados de forma consistente

### Exemplo de Uso

```php
use App\Services\CnpjService;

$cnpjService = new CnpjService();

// Consulta usando fallback (primeiro tenta CNPJ.WS, depois Brasil API)
$resultado = $cnpjService->consultaCnpj('12.345.678/0001-95');

// Consulta usando apenas a Brasil API
$resultado = $cnpjService->consultaCnpj('12.345.678/0001-95', 'brasilapi');

// Consulta usando apenas a CNPJ.WS
$resultado = $cnpjService->consultaCnpj('12.345.678/0001-95', 'cnpjws');

if ($resultado['sucesso']) {
    echo "Dados obtidos via: " . $resultado['origem'];
    print_r($resultado['dados']);
} else {
    echo "Falha na consulta: " . $resultado['mensagem'];
}
```

## Componente Livewire

O componente `App\Livewire\ConsultaCnpj` demonstra o uso prático do serviço em uma interface web:

- Campo para entrada do CNPJ com máscara
- Seletor para escolher qual API usar
- Exibição formatada dos dados retornados
- Tratamento de erros e estado de carregamento

## Formato dos Dados

O serviço padroniza os dados retornados pelas APIs em um formato consistente:

```php
[
    'origem' => 'cnpjws|brasilapi',
    'dados' => [
        'cnpj' => 'string',
        'razao_social' => 'string',
        'nome_fantasia' => 'string',
        'situacao_cadastral' => 'string',
        'data_situacao_cadastral' => 'string',
        'logradouro' => 'string',
        'numero' => 'string',
        'complemento' => 'string',
        'bairro' => 'string',
        'cep' => 'string',
        'uf' => 'string',
        'municipio' => 'string',
        'ddd' => 'string',
        'situacao_especial' => 'string',
        'data_situacao_especial' => 'string',
        'porte' => 'string',
        'natureza_juridica' => 'string',
        'qualificacao_do_responsavel' => 'string',
        'capital_social' => 'float',
        'ente_federativo_responsavel' => 'string',
        'data_inicio_atividade' => 'string',
        'cnae_fiscal' => 'string',
        'cnae_fiscal_descricao' => 'string',
        'socios' => [
            // Array de sócios com nome, cpf_cnpj, etc.
        ]
    ],
    'sucesso' => true|false,
    'mensagem' => 'string' // Apenas quando sucesso=false
]
```

## Testes

O arquivo `tests/Unit/CnpjServiceTest.php` contém testes unitários para a classe CnpjService, verificando:

- Consultas bem-sucedidas
- Tratamento de dados inválidos
- Tratamento de formato de CNPJ inválido

## Considerações

- A implementação permite fácil troca entre APIs sem afetar o restante da aplicação
- O fallback entre APIs aumenta a confiabilidade do serviço
- As limitações de cada API foram consideradas na implementação
- Os dados dos sócios são formatados consistentemente entre as APIs