<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelatorioPerdasRepository
{
    public function consultarPorPeriodo(string $inicio, string $fim): Collection
    {
        return DB::table('encomendas')
            ->selectRaw('DATE(data_entrega) as data, status, COUNT(*) as total_pedidos, COALESCE(SUM(quantidade), 0) as total_frangos')
            ->whereBetween('data_entrega', [$inicio, $fim])
            ->groupByRaw('DATE(data_entrega), status')
            ->orderBy('data')
            ->get();
    }
}
