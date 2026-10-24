<?php

namespace App\Repositories;

use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClienteRepository
{
  public function listar(?string $busca = null): LengthAwarePaginator
  {
    return Cliente::query()
      ->when($busca, function ($query) use ($busca) {
        $query->where(function ($q) use ($busca) {
          $q->where('nome', 'like', "%{$busca}%")
            ->orWhere('telefone', 'like', "%{$busca}%");
        });
      })
      ->orderByDesc('status')
      ->orderBy('nome')
      ->paginate(15)
      ->withQueryString();
  }

  public function buscarPorId(int $id): Cliente
  {
    return Cliente::findOrFail($id);
  }

  public function criar(array $dados): Cliente
  {
    return Cliente::create($dados);
  }

  public function atualizar(Cliente $cliente, array $dados): bool
  {
    return $cliente->update($dados);
  }

  public function possuiEncomendas(Cliente $cliente): bool
  {
    return $cliente->encomendas()->exists();
  }

  public function excluir(Cliente $cliente): bool
  {
    return $cliente->delete();
  }
}
