<?php

namespace App\Services;

use App\Models\Encomenda;
use App\Repositories\ClienteRepository;
use App\Repositories\EncomendaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EncomendaService
{
    public function __construct(
        private EncomendaRepository $encomendas,
        private ClienteRepository $clientes
    ) {}

    public function doDia(string $data): Collection
    {
        return $this->encomendas->doDia($data);
    }

    public function indicadores(string $data): array
    {
        $pedidos = $this->doDia($data);

        return [
            'total' => $pedidos->count(),
            'entregues' => $pedidos->where('status', 'entregue')->count(),
            'pendentes' => $pedidos->where('status', 'em_andamento')->count(),
            'nao_retirados' => $pedidos->where('status', 'nao_retirado')->count(),
            'frangos_nao_retirados' => $pedidos->where('status', 'nao_retirado')->sum('quantidade'),
        ];
    }

    public function criar(array $dados): Encomenda
    {
        $cliente = $this->clientes->buscarPorId((int) $dados['id_cliente']);
        if (!$cliente->status) {
            throw ValidationException::withMessages(['id_cliente' => 'Selecione um cliente ativo.']);
        }

        $dados['status'] = 'em_andamento';
        $dados['endereco'] = $dados['tipo_entrega'] === 'entrega' ? trim($dados['endereco'] ?? '') : null;
        $dados['observacoes'] = trim($dados['observacoes'] ?? '') ?: null;

        return $this->encomendas->criar($dados);
    }

    public function alterarStatus(int $id, string $status): Encomenda
    {
        $encomenda = $this->encomendas->buscar($id);
        return $this->encomendas->alterarStatus($encomenda, $status);
    }
}
