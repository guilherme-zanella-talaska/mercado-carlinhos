<?php

namespace App\Http\Controllers;

use App\Services\RelatorioPerdasService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatorioPerdasController extends Controller
{
    public function __construct(private RelatorioPerdasService $service) {}

    public function index(Request $request): View
    {
        $dados = $request->validate([
            'periodo' => ['sometimes', 'required', 'in:mes,trimestre,semestre,ano'],
        ]);

        return view('relatorios.perdas', [
            'relatorio' => $this->service->gerar($dados['periodo'] ?? 'mes'),
        ]);
    }
}
