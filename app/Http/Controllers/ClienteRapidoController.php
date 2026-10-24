<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteRapidoController extends Controller
{
    public function store(Request $request, ClienteService $service): JsonResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'telefone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{4,5}-\d{4}$/', 'max:20'],
        ]);

        $cliente = $service->criar($dados);

        return response()->json([
            'message' => 'Cliente cadastrado com sucesso!',
            'cliente' => [
                'id' => $cliente->id,
                'nome' => $cliente->nome,
                'telefone' => $cliente->telefone,
            ],
        ], 201);
    }
}
