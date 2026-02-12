<div class="max-w-4xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Consulta de CNPJ</h2>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="md:col-span-2">
                <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">
                    CNPJ
                </label>
                <input
                    type="text"
                    id="cnpj"
                    wire:model="cnpjInput"
                    wire:keydown.enter="consultaCnpj"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="00.000.000/0001-00"
                    maxlength="18"
                    x-data
                    x-mask="00.000.000/00000-00"
                />
                @error('cnpjInput')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-end">
                <button
                    wire:click="consultaCnpj"
                    wire:loading.attr="disabled"
                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                    :disabled="$loading"
                >
                    <span wire:loading.remove>Consultar</span>
                    <span wire:loading>Consultando...</span>
                </button>
            </div>
        </div>

        @if($loading)
            <div class="flex justify-center my-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>
        @endif

        @if($error)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ $error }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($result && $result['sucesso'])
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            Dados obtidos com sucesso
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Dados da Empresa
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Informações cadastrais da empresa
                    </p>
                </div>
                <div class="px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                CNPJ
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['cnpj'] }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Razão Social
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['razao_social'] }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Nome Fantasia
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['nome_fantasia'] }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Situação Cadastral
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['situacao_cadastral'] }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Data Início Atividade
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['data_inicio_atividade'] }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Endereço
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['logradouro'] }}, {{ $result['dados']['numero'] }}
                                @if($result['dados']['complemento'])
                                    - {{ $result['dados']['complemento'] }}
                                @endif
                                <br>
                                {{ $result['dados']['bairro'] }}, {{ $result['dados']['municipio'] }} - {{ $result['dados']['uf'] }}
                                <br>
                                CEP: {{ $result['dados']['cep'] }}
                            </dd>
                        </div>

                        @if(!empty($result['dados']['inscricao_estadual']))
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">
                                Inscrição Estadual
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $result['dados']['inscricao_estadual'] }}
                            </dd>
                        </div>
                        @endif

                        @if($result['dados']['socios'] && count($result['dados']['socios']) > 0)
                        <div class="py-4 sm:py-5 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-medium text-gray-900">Sócios</h4>
                        </div>
                        @foreach($result['dados']['socios'] as $socio)
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ $socio['nome'] }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    CPF/CNPJ: {{ $socio['cpf_cnpj_socio'] ?? 'N/A' }}
                                    <br>
                                    Qualificação: {{ $socio['qualificacao_socio']['descricao'] ?? 'N/A' }}
                                    <br>
                                    Data Entrada: {{ $socio['data_entrada'] }}
                                </dd>
                            </div>
                        @endforeach
                        @endif
                    </dl>
                </div>
            </div>
        @endif
    </div>
</div>
