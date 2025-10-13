# Projeto Laravel para Consulta de CNPJ com Filament

## Visão Geral

Este é um projeto Laravel com Filament instalado, desenvolvido para realização de buscas de dados de CNPJ (Cadastro Nacional da Pessoa Jurídica). O projeto utiliza o Filament como framework admin para prover uma interface administrativa poderosa e amigável.

## Tecnologias Utilizadas

- Laravel 12.x
- Filament 4.1+
- PHP 8.2+
- Composer
- NPM
- MySQL (ou outro banco de dados configurado)

## Estrutura do Projeto

```
cnpj/
├── app/
│   ├── Http/          # Controladores, Middleware, etc.
│   ├── Livewire/      # Componentes Livewire (ações, autenticação, configurações)
│   │   ├── Actions/   # Ações reutilizáveis
│   │   ├── Auth/      # Componentes de autenticação
│   │   └── Settings/  # Componentes de configuração
│   ├── Models/        # Modelos Eloquent
│   └── Providers/     # Provedores de Serviço
├── bootstrap/
├── config/            # Arquivos de configuração
├── database/          # Migrations, seeds e factories
├── public/            # Arquivos públicos
├── resources/         # Views, assets, linguagens
├── routes/            # Arquivos de rota
├── storage/           # Arquivos armazenados
├── tests/             # Testes
├── vendor/            # Pacotes Composer
├── artisan            # Comando Artisan
├── composer.json      # Dependências do projeto
├── .env               # Variáveis de ambiente
├── package.json       # Dependências do Node.js
├── vite.config.js     # Configuração do Vite
└── QWEN.md            # Este arquivo
```

## Configuração e Execução

### Pré-requisitos

- PHP 8.2+
- Composer
- NPM
- Banco de dados (MySQL, PostgreSQL ou SQLite)

### Instalação

1. Instale as dependências do PHP:
```bash
composer install
```

2. Instale as dependências do Node.js:
```bash
npm install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no arquivo .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cnpj
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. Execute as migrations:
```bash
php artisan migrate
```

6. Execute o projeto:
```bash
npm run dev
php artisan serve
```

### Comandos Úteis

- `composer setup`: Instala dependências, gera key, executa migrations e build assets
- `composer dev`: Inicia o servidor Laravel, fila de processos, logs e Vite simultaneamente
- `composer test`: Executa os testes do projeto
- `php artisan filament:install`: Instala o Filament
- `php artisan make:filament-resource`: Cria um novo recurso do Filament
- `php artisan serve`: Inicia o servidor de desenvolvimento
- `npm run build`: Compila os assets para produção
- `npm run dev`: Compila os assets e observa as alterações

### Arquivo .env

O arquivo .env contém as configurações de ambiente do projeto:

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:MILa6yL2wvLtuURpLTyYcsqJQuy3DJOEwhSdESZUGpQ=
APP_DEBUG=true
APP_URL=http://localhost:8004

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cnpj
DB_USERNAME=root
DB_PASSWORD=root
```

## Desenvolvimento

### Recursos do Filament

O Filament permite criar recursos (resources) que representam modelos do Eloquent com interfaces administrativas completas. Para criar um novo recurso:

```bash
php artisan make:filament-resource Empresa --generate
```

### Componentes Livewire

O projeto já inclui estrutura de componentes Livewire organizada em:

- Actions: Componentes de ações reutilizáveis
- Auth: Componentes de autenticação
- Settings: Componentes de configuração

### Convenções de Desenvolvimento

- Siga as convenções de codificação do Laravel
- Use migrations para gerenciar o esquema do banco de dados
- Utilize Models do Eloquent para interagir com o banco de dados
- Utilize Resources do Filament para criar interfaces administrativas
- Siga as melhores práticas de segurança do Laravel
- Use o padrão PSR-4 para namespaces
- Utilize Type Hints e DocBlocks apropriados

## Recursos Específicos para CNPJ

O projeto está estruturado para implementar recursos específicos para busca e manipulação de dados de CNPJ, que incluirão:

- Formulários para busca de dados de empresas por CNPJ
- Integração com APIs de consulta de CNPJ
- Histórico de consultas realizadas
- Exportação de dados
- Autenticação e autorização adequadas

## Cores da Aplicação

O projeto utiliza o seguinte esquema de cores:

- Cor principal escura: #111424
- Cor de destaque: #0EE57F (verde vibrante)

## Logo da Aplicação

A logo da aplicação está armazenada em:

- Caminho: `public/img/logo.png`
- Formato: PNG
- Utilização: Exibida na página inicial de busca de CNPJ

## Arquivo .htaccess

Um arquivo .htaccess adequado para o Laravel foi configurado com:

- Reescrita de URLs amigáveis
- Proteção contra acesso direto a arquivos sensíveis
- Cabeçalhos de segurança
- Compressão de conteúdo
- Cache de conteúdo estático
- Proteção contra injeção de cabeçalhos HTTP

## Resolução de Erros Comuns em Produção

### Erro 403 (Forbidden) em Produção

Se ocorrer erro 403 ao acessar o site em produção, verifique os seguintes itens:

#### 1. Permissões de Diretórios

Execute os seguintes comandos para definir as permissões corretas:

```bash
# Definir permissões para diretórios e arquivos
find /path/to/your/project -type d -exec chmod 755 {} \;
find /path/to/your/project -type f -exec chmod 644 {} \;

# Garantir permissões de escrita para diretórios necessários
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Definir o proprietário correto (substitua www-data pelo usuário do servidor)
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

#### 2. Configuração do Virtual Host

Certifique-se de que o virtual host do Apache permite sobrescrita:

```apache
<Directory "/path/to/your/project/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

#### 3. Diretivas do .htaccess

Verifique se o .htaccess na raiz e no diretório `public` estão configurados corretamente para produção, com as regras de reescrita adequadas.

#### 4. Verifique o Error Log

Consulte os logs de erro do Apache para obter mais detalhes:

```bash
tail -f /var/log/apache2/error.log
```

ou

```bash
tail -f /var/log/httpd/error_log
```

## APIs de Consulta de CNPJ

Para a integração com dados reais de empresas, o projeto utilizará o OpenCNPJ, uma API pública gratuita e sem limites de consultas.

### OpenCNPJ (fulviocanducci/opencnpj)

- **Descrição:** API gratuita e sem limites de consultas para dados de CNPJ
- **Características:** 
  - Dados da Receita Federal
  - Acesso direto às bases governamentais
  - Gratuito e sem limites de uso
  - Sem necessidade de autenticação
  - Dados estruturados em modelos PHP
- **Vantagens:**
  - Não tem custos
  - Não tem limites de uso
  - Dados oficiais da Receita Federal
  - Integração direta com Laravel via Service Provider
  - Não requer CAPTCHA
- **Desvantagens:**
  - Pode ter disponibilidade limitada dependendo do volume de acesso
  - Pode ser mais lento que APIs comerciais devido à natureza gratuita e sem limites

### Implementação no Laravel

**Instalação:**
```bash
composer require fulviocanducci/opencnpj
```

**Exemplo de Controller para Consulta:**
```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Canducci\\OpenCnpj\\CnpjService;
use Illuminate\\Http\\Request;

class CnpjController extends Controller
{
    public function search(Request $request, CnpjService $cnpjService)
    {
        $request->validate([
            'cnpj' => 'required|string'
        ]);

        $response = $cnpjService->get($request->cnpj);

        if ($response->isValid()) {
            return response()->json([
                'success' => true,
                'data' => $response->getCompany()->toArray()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response->getException()->getMessage()
        ], 400);
    }
}
```

**Rota da API:**
```php
use App\\Http\\Controllers\\CnpjController;

Route::get('/cnpj/{cnpj}', [CnpjController::class, 'search']);
```

### Outras Opções Consideradas

#### CNPJ Grátis (jansenfelipe/cnpj-gratis)
- Consulta direta à Receita Federal
- Requer resolução de CAPTCHA, o que limita o uso automatizado
- Não recomendado para uso em produção devido à limitação de CAPTCHA

## Comandos Personalizados

O projeto inclui alguns comandos Composer personalizados:

- `composer setup`: Configura o projeto completamente (instalação de dependências, configuração do ambiente, migrations e build de assets)
- `composer dev`: Executa o servidor Laravel, fila de processos, logs e Vite em modo de desenvolvimento
- `composer test`: Executa os testes do projeto