<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#F7F8FA">

  <title>@yield('title', 'Início') | Mercado Carlinhos</title>

  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">

  @stack('styles')
</head>

<body>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="app-layout">

    <aside class="sidebar" id="sidebar">

      <div class="sidebar-header">

        <a href="{{ route('dashboard') }}" class="brand" title="Mercado Carlinhos">
          <span class="brand-icon">
            <i class="bi bi-shop"></i>
          </span>

          <span class="brand-text">
            <span class="brand-name">Mercado Carlinhos</span>
            <span class="brand-subtitle">Gestão de encomendas</span>
          </span>
        </a>

        <div class="sidebar-header-actions">
          <button type="button" class="sidebar-toggle" id="sidebarToggle" title="Recolher menu"
            aria-label="Recolher menu" aria-expanded="true" aria-controls="sidebar">
            <i class="bi bi-chevron-left"></i>
          </button>

          <button type="button" class="sidebar-close" id="sidebarClose" title="Fechar menu" aria-label="Fechar menu">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

      </div>

      <div class="nav-label">MENU PRINCIPAL</div>

      <nav class="nav-menu" aria-label="Menu principal">

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
          title="Início" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
          <i class="bi bi-grid-1x2 nav-icon"></i>
          <span class="nav-text">Início</span>
        </a>

        <a href="{{ route('relatorios.perdas') }}"
          class="nav-link {{ request()->routeIs('relatorios.perdas') ? 'active' : '' }}" title="Relatório de perdas">
          <i class="bi bi-bar-chart-line nav-icon"></i>
          <span class="nav-text">Relatório de perdas</span>
        </a>

        <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
          title="Clientes" @if(request()->routeIs('clientes.*')) aria-current="page" @endif>
          <i class="bi bi-people nav-icon"></i>
          <span class="nav-text">Clientes</span>
        </a>
      </nav>

      <div class="sidebar-footer">

        <div class="user-profile">
          <div class="avatar">
            {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
          </div>

          <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-role">Administrador</div>
          </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
          @csrf

          <button type="submit" class="btn btn-secondary btn-block sidebar-logout" title="Sair do sistema">
            <i class="bi bi-box-arrow-right"></i>
            <span class="logout-text">Sair do sistema</span>
          </button>
        </form>

      </div>

    </aside>

    <div class="main">

      <div class="mobile-toolbar">
        <button type="button" class="mobile-menu-button" id="menuButton" aria-label="Abrir menu" aria-expanded="false"
          aria-controls="sidebar">
          <i class="bi bi-list"></i>
        </button>

        <span>Mercado Carlinhos</span>
      </div>

      <main class="page-content">
        @yield('content')
      </main>

    </div>

  </div>

  @include('components.toast')

  <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>

  @stack('scripts')

</body>

</html>