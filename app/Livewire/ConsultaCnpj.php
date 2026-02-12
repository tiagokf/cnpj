<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CnpjService;

class ConsultaCnpj extends Component
{
    public $cnpjInput = '';
    public $result = null;
    public $error = null;
    public $loading = false;

    protected $cnpjService;

    public function boot(CnpjService $cnpjService)
    {
        $this->cnpjService = $cnpjService;
    }

    public function mount()
    {
        $this->result = null;
        $this->error = null;
    }

    public function consultaCnpj()
    {
        $this->validate([
            'cnpjInput' => 'required|digits:14'
        ], [
            'cnpjInput.digits' => 'O CNPJ deve ter 14 dígitos.'
        ]);

        $this->loading = true;
        $this->error = null;

        try {
            $this->result = $this->cnpjService->consultaCnpj($this->cnpjInput);

            if (!$this->result['sucesso']) {
                $this->error = $this->result['mensagem'];
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.consulta-cnpj');
    }
}
