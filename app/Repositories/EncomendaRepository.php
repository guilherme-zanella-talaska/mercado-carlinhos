<?php

namespace App\Repositories;

use App\Models\Encomenda;
use Illuminate\Database\Eloquent\Collection;

class EncomendaRepository
{
    private function consultaDoDia(string $data)
    {
        return Encomenda::query()->with('cliente')
            ->whereDate('data_entrega', $data)
            ->orderByRaw("CASE status WHEN 'em_andamento' THEN 1 WHEN 'entregue' THEN 2 WHEN 'nao_retirado' THEN 3 WHEN 'cancelado' THEN 4 ELSE 5 END")
            ->orderBy('horario_entrega')
            ->orderBy('id');
    }

    public function doDia(string $data): Collection
    {
        return $this->consultaDoDia($data)->get();
    }

    public function buscar(int $id): Encomenda
    {
        return Encomenda::with('cliente')->findOrFail($id);
    }

    public function criar(array $dados): Encomenda
    {
        return Encomenda::create($dados);
    }

    public function alterarStatus(Encomenda $encomenda, string $status): Encomenda
    {
        $encomenda->update(['status' => $status]);
        return $encomenda->refresh();
    }
}
