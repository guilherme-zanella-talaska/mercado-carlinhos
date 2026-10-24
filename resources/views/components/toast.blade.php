@php
  $flashMessages = [
    'success' => session('success'),
    'error' => session('error'),
    'warning' => session('warning'),
    'info' => session('info'),
  ];
@endphp

<div id="toastContainer" class="toast-container" role="region" aria-label="Notificações" aria-live="polite"
  data-messages="{{ json_encode($flashMessages) }}">
</div>