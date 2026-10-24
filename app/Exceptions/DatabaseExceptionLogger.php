<?php

namespace App\Exceptions;

use App\Models\LogException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class DatabaseExceptionLogger
{
  public function registrar(Throwable $exception): void
  {
    try {
      LogException::create([
        'tipo' => get_class($exception),
        'mensagem' => $exception->getMessage(),
        'arquivo' => $exception->getFile(),
        'linha' => $exception->getLine(),
        'metodo' => request()->method(),
        'url' => request()->path(),
        'id_usuario' => Auth::id(),
        'created_at' => now()
      ]);
    } catch (Throwable $erro) {
      Log::channel('single')->error(
        'Falha ao registrar exceção no banco de dados.',
        [
          'excecao_original' => get_class($exception),
          'erro_registro' => $erro->getMessage()
        ]
      );
    }
  }
}