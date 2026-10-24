<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\EncomendaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private EncomendaService $service) {}

    public function index(Request $request): View
    {
        $filtros = $request->validate(['data' => ['sometimes', 'required', 'date_format:Y-m-d']]);
        $data = $filtros['data'] ?? now()->toDateString();

        return view('dashboard.index', [
            'data' => $data,
            'indicadores' => $this->service->indicadores($data),
            'encomendas' => $this->service->doDia($data),
            'clientes' => Cliente::query()->where('status', true)->orderBy('nome')->get(['id', 'nome', 'telefone']),
        ]);
    }
}
