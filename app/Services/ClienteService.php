<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ClienteService
{
  public function __construct(
    private ClienteRepository $repository
  ) {
  }

  public function listar(?string $busca = null): LengthAwarePaginator
  {
    return $this->repository->listar($busca);
  }

  public function buscarPorId(int $id): Cliente
  {
    return $this->repository->buscarPorId($id);
  }

  public function criar(array $dados): Cliente
  {
    $dados['status'] = true;

    return $this->repository->criar($dados);
  }

  public function atualizar(int $id, array $dados): bool
  {
    $cliente = $this->repository->buscarPorId($id);

    return $this->repository->atualizar($cliente, $dados);
  }

  public function excluir(int $id): bool
  {
    $cliente = $this->repository->buscarPorId($id);

    if ($this->repository->possuiEncomendas($cliente)) {
      throw ValidationException::withMessages([
        'exclusao' => 'Este cliente possui encomendas. Desative o cadastro para preservar o histórico.',
      ]);
    }

    return $this->repository->excluir($cliente);
  }
}
