@extends('layouts.app')

@section('title', 'Relatório de perdas')

@section('content')
  <div class="page-heading">
    <div>
      <h1>Relatório de perdas</h1>
      <p class="subtitle">Acompanhe os pedidos entregues e os que não foram retirados.</p>
    </div>
  </div>

  <div class="card rp-filter-card">
    <form method="GET" action="{{ route('relatorios.perdas') }}" class="rp-filter-form">
      <div class="form-group">
        <label class="form-label" for="periodoRelatorio">Período de análise</label>
        <select id="periodoRelatorio" name="periodo" class="form-control" onchange="this.form.requestSubmit()">
          <option value="mes" @selected($relatorio['periodo'] === 'mes')>Último mês</option>
          <option value="trimestre" @selected($relatorio['periodo'] === 'trimestre')>Último trimestre</option>
          <option value="semestre" @selected($relatorio['periodo'] === 'semestre')>Último semestre</option>
          <option value="ano" @selected($relatorio['periodo'] === 'ano')>Último ano</option>
        </select>
      </div>
      <span class="rp-period-label"><i class="bi bi-calendar3"></i> {{ $relatorio['inicio'] }} até {{ $relatorio['fim'] }}</span>
    </form>
  </div>

  <div class="stats-grid rp-stats">
    <div class="card stat-card">
      <div class="stat-icon"><i class="bi bi-receipt"></i></div>
      <div class="stat-label">Pedidos solicitados</div>
      <div class="stat-value">{{ $relatorio['totais']['pedidos'] }}</div>
      <div class="stat-detail">Inclui todas as situações, inclusive cancelados</div>
    </div>
    <div class="card stat-card">
      <div class="stat-icon rp-icon-success"><i class="bi bi-check-circle"></i></div>
      <div class="stat-label">Pedidos entregues</div>
      <div class="stat-value">{{ $relatorio['totais']['entregues'] }}</div>
      <div class="stat-detail">Situação atual: Entregue</div>
    </div>
    <div class="card stat-card">
      <div class="stat-icon rp-icon-warning"><i class="bi bi-exclamation-triangle"></i></div>
      <div class="stat-label">Pedidos não retirados</div>
      <div class="stat-value">{{ $relatorio['totais']['nao_retirados'] }}</div>
      <div class="stat-detail">Possíveis perdas de frangos</div>
    </div>
  </div>

  <div class="card rp-chart-card">
    <div class="rp-chart-heading">
      <div>
        <h2 class="card-title">Pedidos por período</h2>
        <p class="subtitle">Comparativo de pedidos solicitados, entregues e não retirados.</p>
      </div>
    </div>
    @if($relatorio['totais']['pedidos'] > 0)
      <div class="rp-chart-wrapper"><canvas id="graficoPerdas" role="img" aria-label="Gráfico de pedidos, entregues e não retirados"></canvas></div>
    @else
      <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-bar-chart"></i></div><h3>Nenhum pedido neste período</h3><p>Selecione outro período para consultar.</p></div>
    @endif
  </div>

  <div class="card rp-waste-note">
    <div class="rp-waste-icon"><i class="bi bi-info-circle"></i></div>
    <div>
      <h2>{{ $relatorio['totais']['frangos_nao_retirados'] }} {{ $relatorio['totais']['frangos_nao_retirados'] === 1 ? 'frango associado' : 'frangos associados' }} a pedidos não retirados</h2>
      <p>Esse número representa frangos encomendados em pedidos marcados como “Não retirado”. Não comprova que todos foram descartados. Os resultados consideram a <strong>data prevista de entrega</strong> e a <strong>situação atual</strong> de cada encomenda.</p>
    </div>
  </div>

  <div id="relatorioPerdasDados" data-grafico="{{ json_encode($relatorio['grafico']) }}" hidden></div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/relatorio-perdas.css') }}?v={{ filemtime(public_path('css/relatorio-perdas.css')) }}">
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
  <script src="{{ asset('js/relatorio-perdas.js') }}?v={{ filemtime(public_path('js/relatorio-perdas.js')) }}"></script>
@endpush
