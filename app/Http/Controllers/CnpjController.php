<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CnpjService;
use Illuminate\Http\Request;

class CnpjController extends Controller
{
    public function __construct(
        protected CnpjService $cnpjService
    ) {}

    public function search(Request $request, string $cnpj)
    {
        $result = $this->cnpjService->getCompanyData($cnpj, source: 'api');

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'],
        ], 400);
    }
}
