# Sistema de Consulta de CNPJ

Este é um sistema Laravel para consulta de dados de CNPJ (Cadastro Nacional da Pessoa Jurídica) com uma interface elegante e moderna. O sistema permite buscar informações detalhadas de empresas brasileiras a partir do número do CNPJ.

## Recursos

- Interface de busca de CNPJ responsiva e moderna
- Design elegante com cores #111424 e #0EE57F
- Consulta a múltiplas APIs (OpenCNPJ, CNPJ.WS, Brasil API)
- Fallback automático entre APIs para maior confiabilidade
- Exibição de inscrição estadual quando disponível
- Layout profissional com espaçamento adequado
- Exibição organizada de informações da empresa
- Totalmente gratuito e com múltiplas opções de API

## Tecnologias Utilizadas

- [Laravel](https://laravel.com/) - Framework PHP
- [Filament](https://filamentphp.com/) - Framework admin
- [Tailwind CSS](https://tailwindcss.com/) - Framework CSS
- [OpenCNPJ](https://opencnpj.org/) - API de consulta de CNPJ
- [CNPJ.WS](https://cnpj.ws/) - API de consulta de CNPJ
- [Brasil API](https://brasilapi.com.br/) - API de consulta de CNPJ
- [PHP](https://php.net/) 8.2+

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/cnpj.git
```

2. Acesse o diretório do projeto:
```bash
cd cnpj
```

3. Instale as dependências do PHP:
```bash
composer install
```

4. Instale as dependências do Node.js:
```bash
npm install
```

5. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

6. Execute as migrations:
```bash
php artisan migrate
```

7. Compile os assets:
```bash
npm run build
```

8. Execute o projeto:
```bash
php artisan serve
```

## Configuração

O sistema utiliza as seguintes variáveis de ambiente:

- `APP_NAME` - Nome da aplicação
- `DB_CONNECTION` - Tipo de banco de dados
- `DB_HOST` - Host do banco de dados
- `DB_PORT` - Porta do banco de dados
- `DB_DATABASE` - Nome do banco de dados
- `DB_USERNAME` - Usuário do banco de dados
- `DB_PASSWORD` - Senha do banco de dados

## Uso

1. Acesse a página inicial do sistema
2. Digite o CNPJ no formato 00.000.000/0000-00
3. Clique em "Buscar CNPJ"
4. Os dados da empresa serão exibidos na seção de resultados

## API de Consulta

O sistema utiliza a API OpenCNPJ para obter os dados das empresas. A API é gratuita, sem limites de uso e fornece dados diretamente da Receita Federal do Brasil.

## Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Faça commit de suas alterações (`git commit -m 'Add some AmazingFeature'`)
4. Faça push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob os termos da licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Contato

**Tiago Gonçalves**  
Desenvolvedor - TI Remoto  
E-mail: [tiago@tiremoto.com.br](mailto:tiago@tiremoto.com.br)  
Website: [tiremoto.com.br](https://tiremoto.com.br)

## Agradecimentos

- Laravel - Pelo excelente framework web
- OpenCNPJ - Pela API gratuita de consulta de CNPJ
- Filament - Pelo framework admin poderoso
- Tailwind CSS - Pelo framework CSS utilitário

---

<p align="center">
  Desenvolvido com ❤️ por <a href="https://tiremoto.com.br">TI Remoto</a>
</p>