@extends('layouts.app')
@section('title', 'Início')

@section('content')
  <div class="page-heading">
    <div>
      <h1>Visão geral</h1>
      <p class="subtitle">Gerencie os pedidos de frango do mercado.</p>
      <form method="GET" action="{{ route('dashboard') }}" class="dashboard-date-filter">
        <label for="dataFiltro"><i class="bi bi-calendar3"></i> Data dos pedidos</label>
        <input type="date" id="dataFiltro" name="data" value="{{ $data }}" onchange="this.form.requestSubmit()">
      </form>
    </div>
    <button type="button" class="btn btn-primary" id="abrirNovoPedido">
      <i class="bi bi-plus-lg"></i> Novo pedido
    </button>
  </div>

  <div class="stats-grid">
    <div class="card stat-card">
      <div class="stat-icon"><i class="bi bi-receipt"></i></div>
      <div class="stat-label">Total de pedidos</div>
      <div class="stat-value">{{ $indicadores['total'] }}</div>
      <div class="stat-detail">Todos os pedidos desta data</div>
    </div>
    <div class="card stat-card">
      <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
      <div class="stat-label">Entregues</div>
      <div class="stat-value">{{ $indicadores['entregues'] }}</div>
      <div class="stat-detail">Pedidos concluídos</div>
    </div>
    <div class="card stat-card">
      <div class="stat-icon"><i class="bi bi-clock"></i></div>
      <div class="stat-label">Em andamento</div>
      <div class="stat-value">{{ $indicadores['pendentes'] }}</div>
      <div class="stat-detail">Aguardando atendimento</div>
    </div>
  </div>

  <div class="card mc-order-card">
    <div class="mc-order-toolbar">
      <div>
        <h2 class="card-title">Encomendas do dia</h2>
        <p class="subtitle">Altere a situação dos pedidos diretamente aqui. Em andamento aparece primeiro.</p>
      </div>
    </div>

    @if($encomendas->count())
      <div class="table-responsive mc-desktop-orders">
        <table class="customers-table mc-orders-table">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Quantidade</th>
              <th>Horário</th>
              <th>Recebimento</th>
              <th>Observações</th>
              <th class="text-right">Alterar situação</th>
            </tr>
          </thead>
          <tbody>
            @foreach($encomendas as $pedido)
              <tr>
                <td class="customer-name">
                  {{ $pedido->cliente?->nome ?? 'Cliente indisponível' }}
                  <small class="mc-muted">{{ $pedido->cliente?->telefone }}</small>
                </td>
                <td><strong>{{ $pedido->quantidade }}</strong> {{ $pedido->quantidade == 1 ? 'frango' : 'frangos' }}</td>
                <td>{{ substr((string) $pedido->horario_entrega, 0, 5) }}</td>
                <td>
                  {{ $pedido->tipo_entrega === 'retirada' ? 'Retirada' : 'Entrega' }}

                  @if($pedido->endereco)
                    <small class="mc-muted">{{ $pedido->endereco }}</small>
                  @endif
                </td>

                <td>
                  @if(filled($pedido->observacoes))
                    <div class="mc-order-observation">
                      <i class="bi bi-chat-left-text"></i>
                      <span>{{ $pedido->observacoes }}</span>
                    </div>
                  @else
                    <span class="mc-muted">—</span>
                  @endif
                </td>

                <td>
                  <form method="POST" action="{{ route('encomendas.status', $pedido->id) }}" class="mc-status-form"
                    data-status-atual="{{ $pedido->status }}">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-control mc-status-select"
                      aria-label="Situação do pedido de {{ $pedido->cliente?->nome }}">
                      <option value="em_andamento" @selected($pedido->status === 'em_andamento')>Em andamento</option>
                      <option value="entregue" @selected($pedido->status === 'entregue')>Entregue</option>
                      <option value="nao_retirado" @selected($pedido->status === 'nao_retirado')>Não retirado</option>
                      <option value="cancelado" @selected($pedido->status === 'cancelado')>Cancelado</option>
                    </select>
                    <button type="submit" class="btn btn-primary mc-save-status" disabled>Salvar</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mc-mobile-orders">
        @foreach($encomendas as $pedido)
          <article class="mc-mobile-order">
            <div class="mc-mobile-order-heading">
              <div>
                <h3>{{ $pedido->cliente?->nome ?? 'Cliente indisponível' }}</h3>
                <span class="mc-muted">{{ $pedido->cliente?->telefone }}</span>
              </div>
              <span class="mc-status mc-status-{{ $pedido->status }}">
                {{ ['em_andamento' => 'Em andamento', 'entregue' => 'Entregue', 'nao_retirado' => 'Não retirado', 'cancelado' => 'Cancelado'][$pedido->status] ?? $pedido->status }}
              </span>
            </div>
            <div class="mc-mobile-order-details">
              <div><span>Quantidade</span><strong>{{ $pedido->quantidade }}
                  {{ $pedido->quantidade == 1 ? 'frango' : 'frangos' }}</strong></div>
              <div><span>Horário</span><strong>{{ substr((string) $pedido->horario_entrega, 0, 5) }}</strong></div>
              <div>
                <span>Recebimento</span><strong>{{ $pedido->tipo_entrega === 'retirada' ? 'Retirada' : 'Entrega' }}</strong>
              </div>
            </div>
            @if($pedido->endereco)
            <p class="mc-mobile-note"><i class="bi bi-geo-alt"></i> {{ $pedido->endereco }}</p>@endif
            @if($pedido->observacoes)
            <p class="mc-mobile-note"><i class="bi bi-chat-left-text"></i> {{ $pedido->observacoes }}</p>@endif
            <form method="POST" action="{{ route('encomendas.status', $pedido->id) }}"
              class="mc-status-form mc-mobile-status-form" data-status-atual="{{ $pedido->status }}">
              @csrf
              @method('PATCH')
              <label class="form-label" for="status-mobile-{{ $pedido->id }}">Alterar situação</label>
              <select id="status-mobile-{{ $pedido->id }}" name="status" class="form-control mc-status-select">
                <option value="em_andamento" @selected($pedido->status === 'em_andamento')>Em andamento</option>
                <option value="entregue" @selected($pedido->status === 'entregue')>Entregue</option>
                <option value="nao_retirado" @selected($pedido->status === 'nao_retirado')>Não retirado</option>
                <option value="cancelado" @selected($pedido->status === 'cancelado')>Cancelado</option>
              </select>
              <button type="submit" class="btn btn-primary mc-save-status" disabled>Salvar alteração</button>
            </form>
          </article>
        @endforeach
      </div>

      @if($indicadores['nao_retirados'])
        <p class="mc-waste-summary"><i class="bi bi-exclamation-circle"></i>
          {{ $indicadores['nao_retirados'] }}
          {{ $indicadores['nao_retirados'] == 1 ? 'pedido não retirado' : 'pedidos não retirados' }}
          ({{ $indicadores['frangos_nao_retirados'] }}
          {{ $indicadores['frangos_nao_retirados'] == 1 ? 'frango' : 'frangos' }}).
          Possíveis perdas, não descarte confirmado.
        </p>
      @endif
    @else
      <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-bag-check"></i></div>
        <h3>Nenhum pedido para esta data</h3>
        <p>Cadastre o primeiro pedido de frango.</p>
        <button type="button" class="btn btn-primary" id="abrirPrimeiroPedido"><i class="bi bi-plus-lg"></i> Novo
          pedido</button>
      </div>
    @endif
  </div>

  @include('encomendas.modal-pedido')
@endsection

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/css/tom-select.css">
  <link rel="stylesheet" href="{{ asset('css/encomendas.css') }}?v={{ filemtime(public_path('css/encomendas.css')) }}">
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/js/tom-select.complete.min.js"></script>
  <script src="{{ asset('js/encomendas.js') }}?v={{ filemtime(public_path('js/encomendas.js')) }}"></script>
@endpush