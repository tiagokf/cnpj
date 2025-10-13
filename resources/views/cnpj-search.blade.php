<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de CNPJ - Sistema de Busca</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #111424 0%, #0a0c17 100%);
            min-height: 100vh;
            color: #ffffff;
        }
        
        .search-card {
            box-shadow: 0 25px 50px -12px rgba(14, 229, 127, 0.3);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(17, 20, 36, 0.95);
        }
        
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(14, 229, 127, 0.5) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #111424 0%, #0a0c17 100%);
            color: #0EE57F;
            border: 2px solid #0EE57F;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0a0c17 0%, #111424 100%);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(14, 229, 127, 0.6);
            transform: translateY(-2px);
        }
        
        .cnpj-input {
            letter-spacing: 2px;
            font-size: 1.25rem;
            background: #0a0c17;
            color: #ffffff;
            border-color: #0EE57F;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(14, 229, 127, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(14, 229, 127, 0); }
            100% { box-shadow: 0 0 0 0 rgba(14, 229, 127, 0); }
        }
        
        .result-card {
            background: linear-gradient(135deg, #111424 0%, #0a0c17 100%);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(14, 229, 127, 0.2);
            border: 1px solid rgba(14, 229, 127, 0.3);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            background: rgba(10, 12, 23, 0.7);
            padding: 1rem;
            border-radius: 12px;
            border-left: 4px solid #0EE57F;
        }
        
        .info-label {
            font-weight: 600;
            color: #0EE57F;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-weight: 500;
            color: #ffffff;
            font-size: 1.125rem;
            margin-top: 0.25rem;
        }
        
        .highlight-section {
            background: linear-gradient(135deg, #0EE57F 0%, #111424 100%);
            color: #111424;
        }
        
        .text-gray-800 { color: #ffffff !important; }
        .text-gray-700 { color: #0EE57F !important; }
        .text-gray-600 { color: #a0aec0 !important; }
        .text-white { color: #ffffff !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-start py-8 px-4">
    <div class="flex justify-center w-full mb-8">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-20 w-auto">
    </div>
    <div class="search-card w-full max-w-4xl mx-auto p-10">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-white mb-3">Consulta de CNPJ</h1>
            <p class="text-gray-300 text-lg">Sistema de busca de informações de empresas</p>
        </div>
        
        <div class="mb-8">
            <form id="cnpj-search-form" class="space-y-6">
                <div class="text-center">
                    <label for="cnpj" class="block text-lg font-medium text-green-400 mb-3">Digite o CNPJ</label>
                    <div class="max-w-md mx-auto relative">
                        <input 
                            type="text" 
                            id="cnpj" 
                            name="cnpj" 
                            placeholder="00.000.000/0000-00"
                            class="w-full px-6 py-4 text-center cnpj-input input-field border-2 border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 text-xl"
                            maxlength="18"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-gray-400">Formato: 00.000.000/0000-00</p>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn-primary text-white font-semibold py-4 px-12 rounded-xl text-lg pulse">
                        Buscar CNPJ
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Seção de resultados - inicialmente oculta -->
        <div id="results-section" class="result-card p-6 hidden">
            <div class="highlight-section p-6 rounded-xl mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-4">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Dados do CNPJ</h3>
                        <p class="text-indigo-100">Informações recuperadas da base de dados</p>
                    </div>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">CNPJ</div>
                    <div class="info-value" id="cnpj-value">00.000.000/0000-00</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Razão Social</div>
                    <div class="info-value" id="razao-social">Nome da Empresa S/A</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nome Fantasia</div>
                    <div class="info-value" id="nome-fantasia">Nome Fantasia</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value" id="status">Ativa</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Data de Abertura</div>
                    <div class="info-value" id="data-abertura">01/01/2022</div>
                </div>
                <div class="info-item">
                    <div class="info-label">CNAE Principal</div>
                    <div class="info-value" id="cnae">0000-0/00</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Natureza Jurídica</div>
                    <div class="info-value" id="natureza-juridica">Entidade Comercial</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Porte da Empresa</div>
                    <div class="info-value" id="porte-empresa">Pequeno Porte</div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4">Endereço</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Logradouro</div>
                        <div class="info-value" id="logradouro">Rua Exemplo</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Número</div>
                        <div class="info-value" id="numero">123</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Complemento</div>
                        <div class="info-value" id="complemento">Sala 101</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Bairro</div>
                        <div class="info-value" id="bairro">Centro</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Cidade</div>
                        <div class="info-value" id="cidade">São Paulo</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Estado</div>
                        <div class="info-value" id="estado">SP</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">CEP</div>
                        <div class="info-value" id="cep">01000-000</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4">Contato</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Telefone</div>
                        <div class="info-value" id="telefone">(11) 99999-9999</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value" id="email">contato@empresa.com.br</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para o campo de CNPJ
            const cnpjInput = document.getElementById('cnpj');
            
            cnpjInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
                
                if (value.length > 14) {
                    value = value.substring(0, 14);
                }
                
                // Aplica a máscara: 00.000.000/0000-00
                if (value.length > 2) {
                    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                }
                if (value.length > 6) {
                    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                }
                if (value.length > 10) {
                    value = value.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3/$4');
                }
                if (value.length > 15) {
                    value = value.replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, '$1.$2.$3/$4-$5');
                }
                
                e.target.value = value;
            });
            
            // Envio do formulário para a API real
            document.getElementById('cnpj-search-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const cnpj = cnpjInput.value.replace(/\D/g, ''); // Remove formatação para envio
                const btn = document.querySelector('.btn-primary');
                const originalText = btn.textContent;
                
                // Mostra estado de carregamento
                btn.textContent = 'Buscando...';
                btn.disabled = true;
                
                fetch(`/api/cnpj/${cnpj}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Preenche os campos com os dados recebidos
                            document.getElementById('cnpj-value').textContent = formatCNPJ(cnpj);
                            document.getElementById('razao-social').textContent = data.data.razao_social || 'Não informado';
                            document.getElementById('nome-fantasia').textContent = data.data.nome_fantasia || 'Não informado';
                            document.getElementById('status').textContent = data.data.situacao || 'Não informado';
                            document.getElementById('data-abertura').textContent = formatDate(data.data.abertura) || 'Não informado';
                            document.getElementById('cnae').textContent = data.data.cnae_principal?.descricao || 'Não informado';
                            document.getElementById('natureza-juridica').textContent = data.data.natureza_juridica || 'Não informado';
                            document.getElementById('porte-empresa').textContent = data.data.porte || 'Não informado';
                            
                            // Endereço
                            document.getElementById('logradouro').textContent = data.data.logradouro || 'Não informado';
                            document.getElementById('numero').textContent = data.data.numero || 'Não informado';
                            document.getElementById('complemento').textContent = data.data.complemento || 'Não informado';
                            document.getElementById('bairro').textContent = data.data.bairro || 'Não informado';
                            document.getElementById('cidade').textContent = data.data.municipio || 'Não informado';
                            document.getElementById('estado').textContent = data.data.uf || 'Não informado';
                            document.getElementById('cep').textContent = formatCEP(data.data.cep) || 'Não informado';
                            
                            // Contato
                            document.getElementById('telefone').textContent = data.data.telefone || 'Não informado';
                            document.getElementById('email').textContent = data.data.email || 'Não informado';
                            
                            // Mostra a seção de resultados
                            document.getElementById('results-section').classList.remove('hidden');
                            
                            // "Rola" para a seção de resultados
                            document.getElementById('results-section').scrollIntoView({ behavior: 'smooth' });
                        } else {
                            alert('Erro na consulta: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Ocorreu um erro ao buscar os dados do CNPJ. Por favor, tente novamente.');
                    })
                    .finally(() => {
                        // Restaura o estado do botão
                        btn.textContent = originalText;
                        btn.disabled = false;
                    });
            });
            
            // Funções auxiliares
            function formatCNPJ(cnpj) {
                if (cnpj.length !== 14) return cnpj;
                return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
            }
            
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('pt-BR');
            }
            
            function formatCEP(cep) {
                if (!cep) return '';
                const cleanCEP = cep.replace(/\D/g, '');
                if (cleanCEP.length !== 8) return cleanCEP;
                return cleanCEP.replace(/^(\d{5})(\d{3})$/, '$1-$2');
            }
        });
    </script>
</body>
</html>