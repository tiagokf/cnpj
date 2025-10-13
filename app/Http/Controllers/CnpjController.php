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
        $request->validate([
            'provider' => 'nullable|string|in:opencnpj,cnpjws,brasilapi'
        ]);

        $result = $this->cnpjService->getCompanyData(
            $cnpj,
            $request->provider
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'provider' => $result['provider']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error']
        ], 400);
    }
}