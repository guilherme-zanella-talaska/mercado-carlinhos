@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
  <div class="page-heading">
    <div>
      <h1>Clientes</h1>
      <p class="subtitle">Consulte e organize os clientes do mercado.</p>
    </div>
    <button type="button" class="btn btn-primary" id="btnNovoCliente">
      <i class="bi bi-plus-lg"></i> Novo cliente
    </button>
  </div>

  <div class="card customers-card">
    <div class="customers-toolbar">
      <div>
        <h2 class="card-title">Clientes cadastrados</h2>
        <p class="subtitle">Encontre um cliente pelo nome ou telefone.</p>
      </div>
      <form method="GET" action="{{ route('clientes.index') }}" class="customer-search">
        <input type="search" name="busca" class="form-control" value="{{ $busca }}"
          placeholder="Nome ou telefone" aria-label="Buscar cliente">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
      </form>
    </div>

    @if($clientes->count())
      <div class="table-responsive customers-desktop">
        <table class="customers-table">
          <thead>
            <tr><th>Cliente</th><th>Telefone</th><th>Status</th><th class="text-right">Ações</th></tr>
          </thead>
          <tbody>
            @foreach($clientes as $cliente)
              <tr>
                <td class="customer-name">{{ $cliente->nome }}</td>
                <td>{{ $cliente->telefone }}</td>
                <td>
                  <span class="badge {{ $cliente->status ? 'badge-success' : 'badge-danger' }}">
                    {{ $cliente->status ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td>
                  <div class="customer-actions">
                    <button type="button" class="btn btn-secondary btn-editar-cliente"
                      data-id="{{ $cliente->id }}" data-nome="{{ $cliente->nome }}"
                      data-telefone="{{ $cliente->telefone }}" data-status="{{ (int) $cliente->status }}"
                      data-url="{{ route('clientes.update', $cliente->id) }}">
                      <i class="bi bi-pencil-square"></i> Editar
                    </button>
                    <form method="POST" action="{{ route('clientes.destroy', $cliente->id) }}"
                      onsubmit="return confirm('Deseja realmente excluir este cliente?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-secondary"><i class="bi bi-trash"></i> Excluir</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="customers-mobile" aria-label="Lista de clientes">
        @foreach($clientes as $cliente)
          <article class="customer-mobile-card">
            <div class="customer-mobile-head">
              <div class="customer-mobile-identity">
                <span class="customer-mobile-avatar" aria-hidden="true"><i class="bi bi-person"></i></span>
                <div class="customer-mobile-name">{{ $cliente->nome }}</div>
              </div>
              <span class="badge {{ $cliente->status ? 'badge-success' : 'badge-danger' }}">
                {{ $cliente->status ? 'Ativo' : 'Inativo' }}
              </span>
            </div>
            <div class="customer-mobile-details">
              <span class="customer-mobile-caption">Telefone</span>
              <a href="tel:{{ preg_replace('/\D+/', '', $cliente->telefone) }}" class="customer-mobile-phone">
                <i class="bi bi-telephone"></i> {{ $cliente->telefone }}
              </a>
            </div>
            <div class="customer-mobile-actions">
              <button type="button" class="btn btn-secondary btn-editar-cliente"
                data-id="{{ $cliente->id }}" data-nome="{{ $cliente->nome }}"
                data-telefone="{{ $cliente->telefone }}" data-status="{{ (int) $cliente->status }}"
                data-url="{{ route('clientes.update', $cliente->id) }}">
                <i class="bi bi-pencil-square"></i> Editar
              </button>
              <form method="POST" action="{{ route('clientes.destroy', $cliente->id) }}"
                onsubmit="return confirm('Deseja realmente excluir este cliente?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary"><i class="bi bi-trash"></i> Excluir</button>
              </form>
            </div>
          </article>
        @endforeach
      </div>

      <div class="customers-pagination">{{ $clientes->links() }}</div>
    @else
      <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-people"></i></div>
        <h3>{{ $busca ? 'Nenhum cliente encontrado' : 'Nenhum cliente cadastrado' }}</h3>
        <p>{{ $busca ? 'Tente buscar por outro nome ou telefone.' : 'Cadastre o primeiro cliente para começar a organizar as encomendas.' }}</p>
        @if($busca)
          <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Limpar pesquisa</a>
        @else
          <button type="button" class="btn btn-primary" id="btnPrimeiroCliente">
            <i class="bi bi-plus-lg"></i> Cadastrar cliente
          </button>
        @endif
      </div>
    @endif
  </div>

  <div class="customer-modal-backdrop" id="modalCliente" hidden>
    <div class="customer-modal" role="dialog" aria-modal="true" aria-labelledby="modalClienteTitulo" tabindex="-1">
      <div class="customer-modal-header">
        <div class="customer-modal-heading">
          <span class="customer-modal-icon"><i class="bi bi-person-plus"></i></span>
          <div><h2 id="modalClienteTitulo">Novo cliente</h2><p>Informe os dados do cliente.</p></div>
        </div>
        <button type="button" class="customer-modal-close" id="fecharModalCliente" aria-label="Fechar modal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <form method="POST" id="formCliente" data-validate novalidate autocomplete="off"
        action="{{ route('clientes.store') }}" data-store-url="{{ route('clientes.store') }}">
        @csrf
        <input type="hidden" name="_method" id="metodoCliente" value="POST">
        <input type="hidden" name="modal_cliente" id="modoCliente" value="novo">
        <input type="hidden" name="cliente_id" id="clienteId" value="">
        <div class="customer-modal-body">
          <div class="form-group">
            <label class="form-label" for="clienteNome">Nome completo *</label>
            <input type="text" id="clienteNome" name="nome" class="form-control"
              placeholder="Ex.: Maria Oliveira" maxlength="150" required>
            @error('nome')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label class="form-label" for="clienteTelefone">Telefone *</label>
            <input type="tel" id="clienteTelefone" name="telefone" class="form-control"
              placeholder="(49) 99999-9999" maxlength="15" inputmode="numeric" autocomplete="tel" required>
            @error('telefone')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-group" id="grupoStatusCliente" hidden>
            <label class="form-label" for="clienteStatus">Situação do cliente</label>
            <select id="clienteStatus" name="status" class="form-control" disabled>
              <option value="1">Ativo</option>
              <option value="0">Inativo</option>
            </select>
            <small class="form-help">Clientes inativos não poderão receber novas encomendas.</small>
            @error('status')<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>
        <div class="customer-modal-footer">
          <button type="button" class="btn btn-secondary" id="cancelarModalCliente">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="salvarCliente">
            <i class="bi bi-check-lg"></i> Cadastrar cliente
          </button>
        </div>
      </form>
    </div>
  </div>

  @php
    $clienteModalConfig = [
      'storeUrl' => route('clientes.store'),
      'updateUrlTemplate' => route('clientes.update', ['cliente' => '__ID__']),
      'oldMode' => old('modal_cliente'),
      'oldId' => old('cliente_id'),
      'oldNome' => old('nome'),
      'oldTelefone' => old('telefone'),
      'oldStatus' => old('status', '1'),
      'hasErrors' => $errors->any(),
    ];
  @endphp
  <div id="clienteModalConfig" data-config="{{ json_encode($clienteModalConfig) }}" hidden></div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/clientes-responsive.css') }}?v={{ filemtime(public_path('css/clientes-responsive.css')) }}">
@endpush

@push('scripts')
  <script src="{{ asset('js/clientes.js') }}?v={{ filemtime(public_path('js/clientes.js')) }}"></script>
@endpush
