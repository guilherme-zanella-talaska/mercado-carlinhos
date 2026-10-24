<?php

namespace App\Http\Controllers;

use App\Services\EncomendaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EncomendaController extends Controller
{
    public function __construct(private EncomendaService $service) {}

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'id_cliente' => ['required', 'integer', 'exists:clientes,id'],
            'quantidade' => ['required', 'integer', 'min:1', 'max:999'],
            'data_entrega' => ['required', 'date_format:Y-m-d'],
            'horario_entrega' => ['required', 'date_format:H:i'],
            'tipo_entrega' => ['required', Rule::in(['retirada', 'entrega'])],
            'endereco' => ['required_if:tipo_entrega,entrega', 'nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->service->criar($dados);

        return redirect()->route('dashboard', ['data' => $dados['data_entrega']])
            ->with('success', 'Pedido cadastrado com sucesso!');
    }

    public function status(Request $request, int $encomenda): RedirectResponse
    {
        $dados = $request->validate([
            'status' => ['required', Rule::in(['em_andamento', 'entregue', 'nao_retirado', 'cancelado'])],
        ]);

        $this->service->alterarStatus($encomenda, $dados['status']);
        return back()->with('success', 'Status do pedido alterado com sucesso!');
    }
}
