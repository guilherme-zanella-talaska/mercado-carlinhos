<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#F7F8FA">
  <title>Login | Mercado Carlinhos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <script src="{{ asset('js/app.js') }}"></script>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>

<body>
  <div class="auth-layout">
    <section class="auth-hero">
      <div class="brand" style="padding:0">
        <span class="brand-icon"><i class="bi bi-shop"></i></span>
        <span>
          <span class="brand-name">Mercado Carlinhos</span>
          <span class="brand-subtitle">Gestão de encomendas</span>
        </span>
      </div>

      <h1>Suas encomendas.<br><span>Tudo sob controle.</span></h1>
      <p>Organize pedidos, acompanhe entregas e tenha mais praticidade na rotina do seu mercado.</p>
    </section>

    <section class="auth-panel">
      <div class="auth-box">
        <div class="brand-icon" style="margin-bottom:26px">
          <i class="bi bi-shield-lock"></i>
        </div>

        <h2>Bem-vindo de volta</h2>
        <p class="auth-description">Entre com suas credenciais para acessar o sistema.</p>

        <form action="{{ route('login.authenticate') }}" method="POST" class="auth-form" data-validate novalidate>
          @csrf

          <div class="form-group">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email" class="form-control" placeholder="Digite seu e-mail"
              value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email')
              <span class="form-error">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Senha</label>
            <input id="password" type="password" name="password" class="form-control" placeholder="Digite sua senha"
              autocomplete="current-password" required>
            @error('password')
              <span class="form-error">{{ $message }}</span>
            @enderror
          </div>

          <button type="submit" class="btn btn-primary btn-block">
            Entrar no sistema <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        <p class="auth-footer">Acesso restrito aos responsáveis do Mercado Carlinhos.</p>
      </div>
    </section>
  </div>
</body>

</html>