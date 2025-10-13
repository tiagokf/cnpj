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